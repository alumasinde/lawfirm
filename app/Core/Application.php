<?php

declare(strict_types=1);

namespace App\Core;

final class Application
{
    private Router $router;
    private Database $database;

    public function __construct(
        private readonly array $config
    ) {
    }

    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }

    public function database(): Database
    {
        return $this->database;
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $value = $this->config;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
