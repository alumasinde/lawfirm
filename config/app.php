<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'app' => [
        'env' => Config::env('APP_ENV', 'production'),
        'debug' => Config::env('APP_DEBUG', false),
        'url' => Config::env('APP_URL', ''),
    ],
    'database' => [
        'host' => Config::env('DB_HOST', '127.0.0.1'),
        'port' => (int) Config::env('DB_PORT', 3306),
        'database' => Config::env('DB_DATABASE', 'lawfirm'),
        'username' => Config::env('DB_USERNAME', ''),
        'password' => Config::env('DB_PASSWORD', ''),
    ],
    'session' => [
        'name' => Config::env('SESSION_NAME', 'lawfirm_session'),
        'secure' => filter_var(Config::env('SESSION_SECURE', false), FILTER_VALIDATE_BOOL),
        'same_site' => Config::env('SESSION_SAME_SITE', 'Lax'),
    ],
    'auth' => [
        'max_attempts' => (int) Config::env('AUTH_MAX_ATTEMPTS', 5),
        'window_minutes' => (int) Config::env('AUTH_WINDOW_MINUTES', 15),
    ],
    'media' => [
        'max_upload_bytes' => (int) Config::env('MEDIA_MAX_UPLOAD_BYTES', 10485760),
    ],
];
