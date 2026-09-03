<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\PublicRepository;
use App\Repositories\SiteRepository;
use App\Services\PublicService;
use App\Services\SiteService;
use InvalidArgumentException;

final class PublicController extends Controller
{
    private PublicService $service;
    private SiteService $site;

    public function __construct(Application $app)
    {
        $this->service = new PublicService(new PublicRepository($app->database()));
        $this->site = new SiteService(new SiteRepository($app->database()));
    }

    protected function view(string $view, array $data = []): string
    {
        return parent::view($view, [
            ...$this->site->layoutData(),
            ...$data,
        ]);
    }

    public function about(Request $request): string
    {
        return $this->page('about_page', 'About the Firm', 'about');
    }

    public function practiceAreas(Request $request): string
    {
        $content = $this->site->content(['practice_areas_page']);

        return $this->view('public/practice-areas', [
            'title' => $content['practice_areas_page']['meta_title'] ?? 'Practice Areas',
            'page' => $content['practice_areas_page'] ?? null,
            'areas' => $this->service->practiceAreas(),
        ]);
    }

    public function advocates(Request $request): string
    {
        $content = $this->site->content(['advocates_page']);

        return $this->view('public/advocates', [
            'title' => $content['advocates_page']['meta_title'] ?? 'Our Advocates',
            'page' => $content['advocates_page'] ?? null,
            'advocates' => $this->service->advocates(),
        ]);
    }

    public function insights(Request $request): string
    {
        $content = $this->site->content(['insights_page', 'insights_more', 'article_fallback']);

        return $this->view('public/insights', [
            'title' => $content['insights_page']['meta_title'] ?? 'Insights & Updates',
            'page' => $content['insights_page'] ?? null,
            'more' => $content['insights_more'] ?? null,
            'fallback' => $content['article_fallback'] ?? null,
            'articles' => $this->service->articles(),
        ]);
    }

    public function faq(Request $request): string
    {
        $content = $this->site->content(['faq_page']);

        return $this->view('public/faq', [
            'title' => $content['faq_page']['meta_title'] ?? 'Frequently Asked Questions',
            'page' => $content['faq_page'] ?? null,
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
            'title' => $area['meta_title'] ?: $area['name'],
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
            'title' => $advocate['meta_title'] ?: $advocate['name'],
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
            'title' => $article['meta_title'] ?: $article['title'],
            'item' => $article,
            'type' => 'Insight',
        ]);
    }

    public function contact(Request $request): string
    {
        $content = $this->site->content(['contact_page', 'contact_details', 'contact_guidance_response', 'contact_guidance_services', 'contact_guidance_privacy']);

        return $this->view('public/contact', [
            'title' => $content['contact_page']['meta_title'] ?? 'Contact Us',
            'page' => $content['contact_page'] ?? null,
            'details' => $content['contact_details'] ?? null,
            'guidance' => array_values(array_filter([
                $content['contact_guidance_response'] ?? null,
                $content['contact_guidance_services'] ?? null,
                $content['contact_guidance_privacy'] ?? null,
            ])),
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

    private function page(string $contentKey, string $fallbackTitle, string $sectionKey): string
    {
        $content = $this->site->content([$contentKey]);
        $page = $content[$contentKey] ?? null;
        $sections = $this->service->sections([$sectionKey]);

        return $this->view('public/about', [
            'title' => $page['meta_title'] ?? $fallbackTitle,
            'page' => $page,
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
