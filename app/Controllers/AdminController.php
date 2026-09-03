<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Application;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\AdminRepository;
use App\Services\AdminService;

final class AdminController extends Controller
{
    private AdminService $service;
    private array $sessionConfig;
    private array $authConfig;

    public function __construct(Application $app)
    {
        $this->service = new AdminService(new AdminRepository($app->database()));
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
        $this->noIndex();
        $user = Auth::user($this->sessionConfig);

        if ($user === null) {
            Response::redirect('/admin/login');
        }

        return View::adminLayout('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            ...$this->service->dashboard(),
        ]);
    }

    public function logout(Request $request): never
    {
        $this->noIndex();

        if (!Csrf::validate((string) $request->input('_token'))) {
            Response::status(419);
            exit('Invalid request token.');
        }

        $user = Auth::user($this->sessionConfig);
        $this->service->auditLogout($user['id'] ?? null);
        Auth::logout($this->sessionConfig);
        Response::redirect('/admin/login');
    }

    private function noIndex(): void
    {
        header('X-Robots-Tag: noindex, nofollow, noarchive', true);
    }
}
