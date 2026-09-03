<?php

declare(strict_types=1);

namespace AppControllers;

use AppCoreApplication;
use AppCoreController;
use AppCoreRequest;
use AppRepositoriesHomepageRepository;
use AppServicesHomepageService;

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
