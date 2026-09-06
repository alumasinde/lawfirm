<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\HomepageRepository;

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
            'slides' => $this->availableSlides(),
            'practiceAreas' => $this->repository->practiceAreas(6),
            'advocates' => $this->repository->advocates(4),
            'articles' => $this->repository->articles(3),
        ];
    }

    private function availableSlides(): array
    {
        $slides = $this->repository->slides();

        foreach ($slides as &$slide) {
            foreach (['image_path', 'mobile_image_path'] as $field) {
                $path = (string) ($slide[$field] ?? '');

                if ($path === '' || !is_file(BASE_PATH . '/public_html' . $path)) {
                    $slide[$field] = null;
                }
            }

            // A mobile image should still be usable if an older slide record has no
            // desktop media_id. This prevents a valid image from being hidden behind
            // the hero fallback while the record is being corrected in admin.
            if ($slide['image_path'] === null && $slide['mobile_image_path'] !== null) {
                $slide['image_path'] = $slide['mobile_image_path'];
            }
        }
        unset($slide);

        return $slides;
    }
}
