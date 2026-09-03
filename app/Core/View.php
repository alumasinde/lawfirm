<?php

declare(strict_types=1);

namespace App\Core;

use App\View\StyleLinker;

final class View
{
    public static function render(string $view, array $data = []): string
    {
        $path = BASE_PATH . '/resources/views/' . trim($view, '/') . '.php';

        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;

        return (string) ob_get_clean();
    }

    public static function layout(string $view, array $data = []): string
    {
        $content = self::render($view, $data);
        $styleLinker = (new StyleLinker())->render();
        $title = $data['title'] ?? 'Webi Wenani & Associates Advocates';

        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/resources/views/layouts/public.php';

        return (string) ob_get_clean();
    }

    public static function adminLayout(string $view, array $data = []): string
    {
        $content = self::render($view, $data);
        $title = $data['title'] ?? 'Administrator';

        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/resources/views/layouts/admin.php';

        return (string) ob_get_clean();
    }
}
