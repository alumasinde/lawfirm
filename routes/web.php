<?php

declare(strict_types=1);

use App\Controllers\AdminController;
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

$app->router()->get('/admin/login', [AdminController::class, 'login']);
$app->router()->post('/admin/login', [AdminController::class, 'authenticate']);
$app->router()->get('/admin', [AdminController::class, 'dashboard']);
$app->router()->post('/admin/logout', [AdminController::class, 'logout']);

$app->router()->get('/admin/manage', [AdminController::class, 'resources']);
$app->router()->get('/admin/content/{resource}', [AdminController::class, 'contentList']);
$app->router()->get('/admin/content/{resource}/create', [AdminController::class, 'contentCreate']);
$app->router()->post('/admin/content/{resource}', [AdminController::class, 'contentStore']);
$app->router()->get('/admin/content/{resource}/{id}/edit', [AdminController::class, 'contentEdit']);
$app->router()->post('/admin/content/{resource}/{id}', [AdminController::class, 'contentUpdate']);
$app->router()->post('/admin/content/{resource}/{id}/delete', [AdminController::class, 'contentDelete']);
