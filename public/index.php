<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/app/bootstrap.php';

require BASE_PATH . '/routes/web.php';

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
