<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private static array $config = [];

    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function start(?array $config = null): void
    {
        if ($config !== null) {
            self::configure($config);
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $config = self::$config;
        $secure = (bool) ($config['secure'] ?? (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

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

    public static function destroy(?array $config = null): void
    {
        self::start($config);
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $cookie = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => $cookie['path'] ?? '/',
                'domain' => $cookie['domain'] ?? '',
                'secure' => (bool) ($cookie['secure'] ?? false),
                'httponly' => (bool) ($cookie['httponly'] ?? true),
                'samesite' => $cookie['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }
}
