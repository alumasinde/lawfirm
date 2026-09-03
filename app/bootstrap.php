<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Config;
use App\Core\Database;
use App\Core\Router;
use App\Core\Session;

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require $path;
    }
});

Config::load(BASE_PATH . '/.env');

$config = require BASE_PATH . '/config/app.php';

Session::configure((array) ($config['session'] ?? []));

$app = new Application($config);
$app->setRouter(new Router());
$app->setDatabase(new Database($config['database']));

return $app;
