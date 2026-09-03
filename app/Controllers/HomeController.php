<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Repositories\HomepageRepository;
use App\Repositories\SiteRepository;
use App\Services\HomepageService;
use App\Services\SiteService;

final class HomeController extends Controller
{
    public function __construct(private readonly Application $app)
    {
    }

    public function index(Request $request): string
    {
        $service = new HomepageService(new HomepageRepository($this->app->database()));
        $site = new SiteService(new SiteRepository($this->app->database()));
        $content = $site->content(['site_identity']);

        return $this->view('home/index', [
            ...$site->layoutData(),
            ...$service->data(),
            'title' => $content['site_identity']['meta_title'] ?? 'Webi Wenani & Associates Advocates',
        ]);
    }
}
