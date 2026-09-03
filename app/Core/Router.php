<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): self
    {
        return $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable|array $handler): self
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = $handler;

        return $this;
    }

    public function dispatch(Request $request): mixed
    {
        $handler = $this->routes[$request->method()][$this->normalize($request->path())] ?? null;

        if ($handler === null) {
            Response::status(404);
            return 'Not Found';
        }

        return is_array($handler)
            ? (new $handler[0])->{$handler[1]}($request)
            : $handler($request);
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }
}
