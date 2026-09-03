<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AdminRepository;

final class AdminService
{
    public function __construct(private readonly AdminRepository $repository)
    {
    }

    public function login(string $email, string $password, string $ip, int $maxAttempts, int $windowMinutes): array
    {
        if ($email === '' || $password === '') {
            return ['ok' => false, 'message' => 'Enter your email and password.'];
        }

        if ($this->repository->failedAttempts($email, $ip, $windowMinutes) >= $maxAttempts) {
            return ['ok' => false, 'message' => 'Too many login attempts. Please try again later.'];
        }

        $user = $this->repository->userByEmail($email);
        $valid = $user !== null
            && $user['status'] === 'active'
            && password_verify($password, $user['password_hash'])
            && in_array('admin', $user['roles'], true);

        $this->repository->recordAttempt($email, $ip, $valid);

        if (!$valid) {
            return ['ok' => false, 'message' => 'Invalid login credentials.'];
        }

        $this->repository->clearFailedAttempts($email, $ip);
        $this->repository->updateLastLogin((int) $user['id']);
        $this->repository->audit((int) $user['id'], 'admin.login');

        return ['ok' => true, 'user' => $user];
    }

    public function dashboard(): array
    {
        return [
            'stats' => $this->repository->stats(),
            'recentInquiries' => $this->repository->recentInquiries(),
        ];
    }

    public function auditLogout(?int $userId): void
    {
        $this->repository->audit($userId, 'admin.logout');
    }
}
