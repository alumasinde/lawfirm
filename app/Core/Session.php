<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    public static function start(array $config = []): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = (bool) ($config['secure'] ?? (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));

        session_name((string) ($config['name'] ?? 'lawfirm_session'));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => (string) ($config['same_site'] ?? 'Lax'),
        ]);
        session_start();
    }

    public static function destroy(array $config = []): void
    {
        self::start($config);
        $_SESSION = [];
        session_destroy();
    }
}
