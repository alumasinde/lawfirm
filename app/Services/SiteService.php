<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SiteRepository;

final class SiteService
{
    public function __construct(private readonly SiteRepository $repository)
    {
    }

    public function content(array $keys): array
    {
        return $this->repository->content($keys);
    }

    public function layoutData(): array
    {
        return [
            'siteContent' => $this->content([
                'site_identity',
                'top_bar',
                'footer_explore',
                'footer_connect',
            ]),
            'navigation' => [
                'main' => $this->repository->navigation('main'),
                'footer_explore' => $this->repository->navigation('footer_explore'),
                'footer_connect' => $this->repository->navigation('footer_connect'),
            ],
        ];
    }
}
