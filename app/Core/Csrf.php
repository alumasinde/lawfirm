<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        Session::start();

        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION[self::KEY];
    }

    public static function validate(?string $token): bool
    {
        Session::start();

        return is_string($token)
            && $token !== ''
            && isset($_SESSION[self::KEY])
            && hash_equals((string) $_SESSION[self::KEY], $token);
    }
}
