<?php

declare(strict_types=1);

namespace App\Core;

final class Response
{
    public static function status(int $status): void
    {
        http_response_code($status);
    }

    public static function redirect(string $url): never
    {
        header('Location: ' . $url, true, 303);
        exit;
    }
}
