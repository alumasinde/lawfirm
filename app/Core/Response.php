<?php

declare(strict_types=1);

namespace AppCore;

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
