<?php

declare(strict_types=1);

use App\Controllers\HomeController;

/** @var App\Core\Application $app */

$app->router()->get('/', [HomeController::class, 'index']);
