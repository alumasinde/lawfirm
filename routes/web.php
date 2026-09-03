<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\PublicController;

$app->router()->get('/', [HomeController::class, 'index']);

$app->router()->get('/about', [PublicController::class, 'about']);

$app->router()->get('/practice-areas', [PublicController::class, 'practiceAreas']);
$app->router()->get('/practice-areas/{slug}', [PublicController::class, 'practiceArea']);

$app->router()->get('/advocates', [PublicController::class, 'advocates']);
$app->router()->get('/advocates/{slug}', [PublicController::class, 'advocate']);

$app->router()->get('/insights', [PublicController::class, 'insights']);
$app->router()->get('/insights/{slug}', [PublicController::class, 'article']);

$app->router()->get('/faq', [PublicController::class, 'faq']);

$app->router()->get('/contact', [PublicController::class, 'contact']);
$app->router()->post('/contact', [PublicController::class, 'submitContact']);
