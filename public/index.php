<?php

declare(strict_types=1);

/*
 * PHP's built-in development server can use this file as its router:
 *
 * php -S 0.0.0.0:8000 -t public public/index.php
 *
 * Existing static files are served directly. Application routes continue
 * through the router below.
 */
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $publicPath = __DIR__ . $requestPath;

    if ($requestPath !== '/' && is_file($publicPath)) {
        return false;
    }
}

$app = require dirname(__DIR__) . '/app/bootstrap.php';

require BASE_PATH . '/routes/web.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (str_starts_with($path, '/admin')) {
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
}

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');

try {
    $response = $app->router()->dispatch(new App\Core\Request(), $app);

    if (is_string($response)) {
        echo $response;
    }
} catch (Throwable $exception) {
    error_log($exception->__toString());

    http_response_code(500);

    if ($app->config('app.debug', false)) {
        echo '<pre>' . htmlspecialchars($exception->__toString(), ENT_QUOTES, 'UTF-8') . '</pre>';
        exit;
    }

    echo 'An unexpected error occurred.';
}
