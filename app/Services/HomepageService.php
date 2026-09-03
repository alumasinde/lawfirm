<?php

declare(strict_types=1);

namespace AppServices;

use AppRepositoriesHomepageRepository;

final class HomepageService
{
    public function __construct(private readonly HomepageRepository $repository)
    {
    }

    public function data(): array
    {
        return [
            'sections' => [
                'hero' => $this->repository->section('hero'),
                'about' => $this->repository->section('about'),
                'practice_areas' => $this->repository->section('practice_areas'),
                'advocates' => $this->repository->section('advocates'),
                'insights' => $this->repository->section('insights'),
                'consultation' => $this->repository->section('consultation'),
            ],
            'slides' => $this->repository->slides(),
            'practiceAreas' => $this->repository->practiceAreas(6),
            'advocates' => $this->repository->advocates(4),
            'articles' => $this->repository->articles(3),
        ];
    }
}
