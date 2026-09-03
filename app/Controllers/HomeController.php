<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Repositories\HomepageRepository;
use App\Services\HomepageService;

final class HomeController extends Controller
{
    public function __construct(private readonly Application $app)
    {
    }

    public function index(Request $request): string
    {
        $service = new HomepageService(
            new HomepageRepository($this->app->database())
        );

        return $this->view('home/index', [
            ...$service->data(),
            'title' => 'Webi Wenani & Associates Advocates | Legal Services',
        ]);
    }
}
