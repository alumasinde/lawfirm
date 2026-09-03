<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    private const USER_KEY = '_admin_user';

    public static function login(array $user, array $config = []): void
    {
        Session::start($config);
        session_regenerate_id(true);
        $_SESSION[self::USER_KEY] = [
            'id' => (int) $user['id'],
            'email' => (string) $user['email'],
            'name' => trim((string) $user['first_name'] . ' ' . (string) $user['last_name']),
            'roles' => array_values($user['roles'] ?? []),
        ];
    }

    public static function user(array $config = []): ?array
    {
        Session::start($config);
        $user = $_SESSION[self::USER_KEY] ?? null;

        return is_array($user) ? $user : null;
    }

    public static function check(array $config = []): bool
    {
        return self::user($config) !== null;
    }

    public static function logout(array $config = []): void
    {
        Session::destroy($config);
    }
}
