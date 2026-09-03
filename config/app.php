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
];
