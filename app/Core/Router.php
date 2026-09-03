<?php

declare(strict_types=1);

namespace AppCore;

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

    public function dispatch(Request $request, Application $app): mixed
    {
        $method = $request->method();
        $path = $this->normalize($request->path());
        $handler = $this->routes[$method][$path] ?? null;
        $params = [];

        if ($handler === null) {
            foreach ($this->routes[$method] ?? [] as $route => $candidate) {
                $pattern = preg_replace('#{[a-zA-Z_][a-zA-Z0-9_]*}#', '([^/]+)', $route);
                if ($pattern !== null && preg_match('#^' . $pattern . '$#', $path, $matches)) {
                    preg_match_all('#{([a-zA-Z_][a-zA-Z0-9_]*)}#', $route, $names);
                    foreach ($names[1] as $index => $name) {
                        $params[$name] = rawurldecode($matches[$index + 1]);
                    }
                    $handler = $candidate;
                    break;
                }
            }
        }

        if ($handler === null) {
            Response::status(404);
            return 'Not Found';
        }

        if (is_array($handler)) {
            $controller = new $handler[0]($app);
            return $controller->{$handler[1]}($request, ...array_values($params));
        }

        return $handler($request, ...array_values($params));
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}
