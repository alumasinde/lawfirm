<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Repositories\AdminContentRepository;
use App\Repositories\AdminRepository;
use App\Repositories\AdminMediaRepository;
use App\Repositories\AdminHomepageRepository;
use App\Services\AdminContentService;
use App\Services\AdminMediaService;
use App\Services\AdminService;
use InvalidArgumentException;

final class AdminController extends Controller
{
    private AdminService $service;
    private AdminContentService $content;
    private AdminMediaService $media;
    private AdminHomepageRepository $homepage;
    private array $sessionConfig;
    private array $authConfig;

    public function __construct(Application $app)
    {
        $this->service = new AdminService(new AdminRepository($app->database()));
        $this->content = new AdminContentService(new AdminContentRepository($app->database()));
        $this->homepage = new AdminHomepageRepository($app->database());
        $this->media = new AdminMediaService(
            new AdminMediaRepository($app->database()),
            max(1, (int) $app->config('media.max_upload_bytes', 10485760))
        );
        $this->sessionConfig = (array) $app->config('session', []);
        $this->authConfig = (array) $app->config('auth', []);
    }

    public function login(Request $request): string
    {
        $this->noIndex();

        if (Auth::check($this->sessionConfig)) {
            Response::redirect('/admin');
        }

        return View::adminLayout('admin/login', [
            'title' => 'Administrator Login',
            'csrfToken' => Csrf::token(),
            'error' => $request->query('error'),
        ]);
    }

    public function authenticate(Request $request): never
    {
        $this->noIndex();

        if (!Csrf::validate((string) $request->input('_token'))) {
            Response::status(419);
            exit('Invalid request token.');
        }

        $result = $this->service->login(
            strtolower(trim((string) $request->input('email'))),
            (string) $request->input('password'),
            (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
            (int) ($this->authConfig['max_attempts'] ?? 5),
            (int) ($this->authConfig['window_minutes'] ?? 15)
        );

        if (!$result['ok']) {
            Response::redirect('/admin/login?error=' . rawurlencode((string) $result['message']));
        }

        Auth::login($result['user'], $this->sessionConfig);
        Response::redirect('/admin');
    }

    public function dashboard(Request $request): string
    {
        $user = $this->requireUser();

        return View::adminLayout('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'resources' => $this->content->resources(),
            ...$this->service->dashboard(),
        ]);
    }

    public function resources(Request $request): string
    {
        $user = $this->requireUser();

        return View::adminLayout('admin/resources', [
            'title' => 'Website Management',
            'user' => $user,
            'resources' => $this->content->resources(),
        ]);
    }

    public function contentList(Request $request, string $resourceKey): string
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);
        $listing = $this->content->list(
            $resource,
            max(1, (int) $request->query('page', 1)),
            (string) $request->query('q', '')
        );

        return View::adminLayout('admin/content-list', [
            'title' => $resource['label'],
            'user' => $user,
            'resources' => $this->content->resources(),
            'resource' => $resource,
            'listing' => $listing,
            'message' => $request->query('message'),
        ]);
    }

    public function contentCreate(Request $request, string $resourceKey): string
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);

        return View::adminLayout('admin/content-form', [
            'title' => 'Add ' . $resource['label'],
            'user' => $user,
            'resources' => $this->content->resources(),
            'resource' => $resource,
            'fields' => $this->content->fields($resource),
            'record' => [],
            'mediaOptions' => $this->media->options(),
            'error' => $request->query('error'),
            'action' => '/admin/content/' . rawurlencode($resourceKey),
            'submitLabel' => 'Create',
        ]);
    }

    public function contentStore(Request $request, string $resourceKey): never
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);
        $this->validateToken($request);

        try {
            $id = $this->content->create($resource, $request->all());
            $this->service->audit((int) $user['id'], 'admin.content.created', [
                'resource' => $resourceKey,
                'id' => $id,
            ]);
            Response::redirect('/admin/content/' . rawurlencode($resourceKey) . '?message=created');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/content/' . rawurlencode($resourceKey) . '/create?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function contentEdit(Request $request, string $resourceKey, string $id): string
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);
        $record = $this->content->find($resource, $this->id($id));

        if ($record === null) {
            $this->notFound();
        }

        return View::adminLayout('admin/content-form', [
            'title' => 'Edit ' . $resource['label'],
            'user' => $user,
            'resources' => $this->content->resources(),
            'resource' => $resource,
            'fields' => $this->content->fields($resource),
            'record' => $record,
            'mediaOptions' => $this->media->options(),
            'action' => '/admin/content/' . rawurlencode($resourceKey) . '/' . (int) $id,
            'submitLabel' => 'Save changes',
            'error' => $request->query('error'),
        ]);
    }

    public function contentUpdate(Request $request, string $resourceKey, string $id): never
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);
        $recordId = $this->id($id);
        $this->validateToken($request);

        try {
            $this->content->update($resource, $recordId, $request->all());
            $this->service->audit((int) $user['id'], 'admin.content.updated', [
                'resource' => $resourceKey,
                'id' => $recordId,
            ]);
            Response::redirect('/admin/content/' . rawurlencode($resourceKey) . '?message=updated');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/content/' . rawurlencode($resourceKey) . '/' . $recordId . '/edit?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function contentDelete(Request $request, string $resourceKey, string $id): never
    {
        $user = $this->requireUser();
        $resource = $this->resourceOr404($resourceKey);
        $recordId = $this->id($id);
        $this->validateToken($request);

        $this->content->delete($resource, $recordId);
        $this->service->audit((int) $user['id'], 'admin.content.deleted', [
            'resource' => $resourceKey,
            'id' => $recordId,
        ]);

        Response::redirect('/admin/content/' . rawurlencode($resourceKey) . '?message=deleted');
    }

    public function homepage(Request $request): string
    {
        $user = $this->requireUser();

        return View::adminLayout('admin/homepage', [
            'title' => 'Homepage Builder',
            'user' => $user,
            'resources' => $this->content->resources(),
            'sections' => $this->homepage->sections(),
            'slides' => $this->homepage->slides(),
            'mediaOptions' => $this->media->options(),
            'nextSortOrder' => $this->homepage->nextSortOrder(),
            'message' => $request->query('message'),
            'error' => $request->query('error'),
        ]);
    }

    public function homepageSectionUpdate(Request $request, string $id): never
    {
        $user = $this->requireUser();
        $this->validateToken($request);
        $sectionId = $this->id($id);

        try {
            $this->homepage->updateSection($sectionId, [
                'title' => $this->nullable($request->input('title')),
                'eyebrow' => $this->nullable($request->input('eyebrow')),
                'body' => $this->nullable($request->input('body')),
                'primary_label' => $this->nullable($request->input('primary_label')),
                'primary_url' => $this->nullable($request->input('primary_url')),
                'secondary_label' => $this->nullable($request->input('secondary_label')),
                'secondary_url' => $this->nullable($request->input('secondary_url')),
                'is_enabled' => $request->input('is_enabled') === '1' ? 1 : 0,
            ]);
            $this->service->audit((int) $user['id'], 'admin.homepage.section.updated', ['id' => $sectionId]);
            Response::redirect('/admin/homepage?message=section');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/homepage?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function homepageSlideStore(Request $request): never
    {
        $user = $this->requireUser();
        $this->validateToken($request);

        try {
            $id = $this->homepage->createSlide($this->slideData($request, $this->homepage->nextSortOrder()));
            $this->service->audit((int) $user['id'], 'admin.homepage.slide.created', ['id' => $id]);
            Response::redirect('/admin/homepage?message=slide');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/homepage?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function homepageSlideUpdate(Request $request, string $id): never
    {
        $user = $this->requireUser();
        $this->validateToken($request);
        $slideId = $this->id($id);

        try {
            $this->homepage->updateSlide($slideId, $this->slideData($request, 0));
            $this->service->audit((int) $user['id'], 'admin.homepage.slide.updated', ['id' => $slideId]);
            Response::redirect('/admin/homepage?message=slide');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/homepage?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function homepageSlideDelete(Request $request, string $id): never
    {
        $user = $this->requireUser();
        $this->validateToken($request);
        $slideId = $this->id($id);
        $this->homepage->deleteSlide($slideId);
        $this->service->audit((int) $user['id'], 'admin.homepage.slide.deleted', ['id' => $slideId]);
        Response::redirect('/admin/homepage?message=slide');
    }

    public function media(Request $request): string
    {
        $user = $this->requireUser();

        return View::adminLayout('admin/media', [
            'title' => 'Media Library',
            'user' => $user,
            'resources' => $this->content->resources(),
            'listing' => $this->media->list(
                max(1, (int) $request->query('page', 1)),
                (string) $request->query('q', '')
            ),
            'message' => $request->query('message'),
            'error' => $request->query('error'),
        ]);
    }

    public function mediaUpload(Request $request): never
    {
        $user = $this->requireUser();
        $this->validateToken($request);

        try {
            $file = $request->file('image');

            if ($file === null) {
                throw new InvalidArgumentException('Choose an image to upload.');
            }

            $id = $this->media->upload($file, (string) $request->input('alt_text'));
            $this->service->audit((int) $user['id'], 'admin.media.uploaded', ['id' => $id]);
            Response::redirect('/admin/media?message=uploaded');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/media?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function mediaDelete(Request $request, string $id): never
    {
        $user = $this->requireUser();
        $mediaId = $this->id($id);
        $this->validateToken($request);

        try {
            $this->media->delete($mediaId);
            $this->service->audit((int) $user['id'], 'admin.media.deleted', ['id' => $mediaId]);
            Response::redirect('/admin/media?message=deleted');
        } catch (InvalidArgumentException $exception) {
            Response::redirect('/admin/media?error=' . rawurlencode($exception->getMessage()));
        }
    }

    public function logout(Request $request): never
    {
        $this->noIndex();
        $this->validateToken($request);

        $user = Auth::user($this->sessionConfig);
        $this->service->auditLogout($user['id'] ?? null);
        Auth::logout($this->sessionConfig);
        Response::redirect('/admin/login');
    }

    private function slideData(Request $request, int $defaultSortOrder): array
    {
        $title = trim((string) $request->input('title'));

        if ($title === '') {
            throw new InvalidArgumentException('Slide title is required.');
        }

        $opacity = (float) $request->input('overlay_opacity', '0.55');

        if ($opacity < 0 || $opacity > 1) {
            throw new InvalidArgumentException('Overlay opacity must be between 0 and 1.');
        }

        return [
            'title' => $title,
            'body' => $this->nullable($request->input('body')),
            'media_id' => $this->nullableInt($request->input('media_id')),
            'mobile_media_id' => $this->nullableInt($request->input('mobile_media_id')),
            'primary_label' => $this->nullable($request->input('primary_label')),
            'primary_url' => $this->nullable($request->input('primary_url')),
            'secondary_label' => $this->nullable($request->input('secondary_label')),
            'secondary_url' => $this->nullable($request->input('secondary_url')),
            'overlay_opacity' => $opacity,
            'is_enabled' => $request->input('is_enabled') === '1' ? 1 : 0,
            'sort_order' => max(0, (int) $request->input('sort_order', $defaultSortOrder)),
        ];
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $id = (int) $value;

        if ($id < 1) {
            throw new InvalidArgumentException('Select a valid media item.');
        }

        return $id;
    }

    private function requireUser(): array
    {
        $this->noIndex();
        $user = Auth::user($this->sessionConfig);

        if ($user === null) {
            Response::redirect('/admin/login');
        }

        return $user;
    }

    private function resourceOr404(string $key): array
    {
        $resource = $this->content->resource($key);

        if ($resource === null) {
            $this->notFound();
        }

        return $resource;
    }

    private function validateToken(Request $request): void
    {
        if (!Csrf::validate((string) $request->input('_token'))) {
            Response::status(419);
            exit('Invalid request token.');
        }
    }

    private function id(string $value): int
    {
        $id = (int) $value;

        if ($id < 1 || (string) $id !== ltrim($value, '0')) {
            $this->notFound();
        }

        return $id;
    }

    private function notFound(): never
    {
        Response::status(404);
        exit('Not Found');
    }

    private function noIndex(): void
    {
        header('X-Robots-Tag: noindex, nofollow, noarchive', true);
    }
}
