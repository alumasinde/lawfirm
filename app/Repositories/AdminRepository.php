<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AdminRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function userByEmail(string $email): ?array
    {
        $user = $this->database->statement(
            'SELECT id, first_name, last_name, email, password_hash, status FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        )->fetch();

        if (!$user) {
            return null;
        }

        $roles = $this->database->statement(
            'SELECT r.name FROM roles r
             INNER JOIN user_roles ur ON ur.role_id = r.id
             WHERE ur.user_id = :user_id',
            ['user_id' => $user['id']]
        )->fetchAll(\PDO::FETCH_COLUMN);

        $user['roles'] = $roles;

        return $user;
    }

    public function updateLastLogin(int $userId): void
    {
        $this->database->statement(
            'UPDATE users SET last_login_at = NOW() WHERE id = :id',
            ['id' => $userId]
        );
    }

    public function failedAttempts(string $email, string $ip, int $minutes): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - ($minutes * 60));

        return (int) $this->database->statement(
            'SELECT COUNT(*) FROM auth_login_attempts
             WHERE email = :email AND ip_address = :ip AND was_successful = 0
             AND attempted_at >= :cutoff',
            ['email' => $email, 'ip' => $ip, 'cutoff' => $cutoff]
        )->fetchColumn();
    }

    public function recordAttempt(string $email, string $ip, bool $successful): void
    {
        $this->database->statement(
            'INSERT INTO auth_login_attempts (email, ip_address, was_successful) VALUES (:email, :ip, :successful)',
            ['email' => $email, 'ip' => $ip, 'successful' => $successful ? 1 : 0]
        );
    }

    public function clearFailedAttempts(string $email, string $ip): void
    {
        $this->database->statement(
            'DELETE FROM auth_login_attempts WHERE email = :email AND ip_address = :ip AND was_successful = 0',
            ['email' => $email, 'ip' => $ip]
        );
    }

    public function stats(): array
    {
        $tables = [
            'practice_areas' => 'Practice areas',
            'advocates' => 'Advocates',
            'articles' => 'Insights',
            'contact_inquiries' => 'New enquiries',
        ];

        $stats = [];
        foreach ($tables as $table => $label) {
            $where = $table === 'contact_inquiries' ? ' WHERE status = "new"' : '';
            $stats[] = [
                'label' => $label,
                'value' => (int) $this->database->statement('SELECT COUNT(*) FROM ' . $table . $where)->fetchColumn(),
            ];
        }

        return $stats;
    }

    public function recentInquiries(): array
    {
        return $this->database->statement(
            'SELECT id, name, email, subject, status, created_at
             FROM contact_inquiries ORDER BY created_at DESC LIMIT 8'
        )->fetchAll();
    }

    public function audit(?int $userId, string $event, array $context = []): void
    {
        $this->database->statement(
            'INSERT INTO audit_logs (user_id, event, context_json, ip_address)
             VALUES (:user_id, :event, :context_json, :ip_address)',
            [
                'user_id' => $userId,
                'event' => $event,
                'context_json' => $context === [] ? null : json_encode($context, JSON_THROW_ON_ERROR),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]
        );
    }
}
