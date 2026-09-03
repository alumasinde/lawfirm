<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\PublicRepository;
use App\Services\PublicService;
use InvalidArgumentException;

final class PublicController extends Controller
{
    private PublicService $service;

    public function __construct(Application $app)
    {
        $this->service = new PublicService(
            new PublicRepository($app->database())
        );
    }

    public function about(Request $request): string
    {
        return $this->page('About the Firm', 'about');
    }

    public function practiceAreas(Request $request): string
    {
        return $this->view('public/practice-areas', [
            'title' => 'Practice Areas',
            'areas' => $this->service->practiceAreas(),
        ]);
    }

    public function advocates(Request $request): string
    {
        return $this->view('public/advocates', [
            'title' => 'Our Advocates',
            'advocates' => $this->service->advocates(),
        ]);
    }

    public function insights(Request $request): string
    {
        return $this->view('public/insights', [
            'title' => 'Insights & Updates',
            'articles' => $this->service->articles(),
        ]);
    }

    public function faq(Request $request): string
    {
        return $this->view('public/faq', [
            'title' => 'Frequently Asked Questions',
            'faqs' => $this->service->faqs(),
        ]);
    }

    public function practiceArea(Request $request, string $slug): string
    {
        $area = $this->service->practiceArea($slug);

        if ($area === null) {
            return $this->notFound();
        }

        return $this->view('public/detail', [
            'title' => $area['name'],
            'item' => $area,
            'type' => 'Practice Area',
        ]);
    }

    public function advocate(Request $request, string $slug): string
    {
        $advocate = $this->service->advocate($slug);

        if ($advocate === null) {
            return $this->notFound();
        }

        $advocate['name'] = trim($advocate['first_name'] . ' ' . $advocate['last_name']);

        return $this->view('public/advocate', [
            'title' => $advocate['name'],
            'advocate' => $advocate,
        ]);
    }

    public function article(Request $request, string $slug): string
    {
        $article = $this->service->article($slug);

        if ($article === null) {
            return $this->notFound();
        }

        return $this->view('public/detail', [
            'title' => $article['title'],
            'item' => $article,
            'type' => 'Insight',
        ]);
    }

    public function contact(Request $request): string
    {
        return $this->view('public/contact', [
            'title' => 'Contact Us',
            'csrfToken' => Csrf::token(),
            'status' => $request->query('status'),
        ]);
    }

    public function submitContact(Request $request): never
    {
        if (!Csrf::validate((string) $request->input('_token'))) {
            Response::status(419);
            exit('Invalid request token.');
        }

        try {
            $this->service->inquiry($request->all());
        } catch (InvalidArgumentException) {
            Response::redirect('/contact?status=invalid');
        }

        Response::redirect('/contact?status=success');
    }

    private function page(string $title, string $sectionKey): string
    {
        $sections = $this->service->sections([$sectionKey]);

        return $this->view('public/about', [
            'title' => $title,
            'section' => $sections[$sectionKey] ?? null,
        ]);
    }

    private function notFound(): string
    {
        Response::status(404);

        return $this->view('errors/404', [
            'title' => 'Page Not Found',
        ]);
    }
}
