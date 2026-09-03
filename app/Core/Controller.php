<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): string
    {
        return View::layout($view, $data);
    }
}
