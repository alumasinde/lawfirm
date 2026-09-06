<?php
declare(strict_types=1);
namespace App\Services;
use App\Repositories\HomepageRepository;
use App\Support\Cache;

final class HomepageService
{
    private const CACHE_KEY = 'public:homepage:v2';
    private const CACHE_TTL = 600;

    public function __construct(private readonly HomepageRepository $repository) {}

    public function data(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            $keys = ['hero', 'about', 'practice_areas', 'advocates', 'insights', 'consultation'];
            $sections = $this->repository->sections($keys);
            foreach ($keys as $key) $sections[$key] ??= null;

            return [
                'sections' => $sections,
                'slides' => $this->availableSlides(),
                'practiceAreas' => $this->repository->practiceAreas(6),
                'advocates' => $this->repository->advocates(4),
                'articles' => $this->repository->articles(3),
            ];
        });
    }

    private function availableSlides(): array
    {
        $slides = $this->repository->slides();

        foreach ($slides as &$slide) {
            foreach (['image_path', 'mobile_image_path'] as $field) {
                $path = (string) ($slide[$field] ?? '');
                if ($path === '' || !is_file(BASE_PATH . '/public_html' . $path)) $slide[$field] = null;
            }

            if ($slide['image_path'] === null && $slide['mobile_image_path'] !== null) {
                $slide['image_path'] = $slide['mobile_image_path'];
            }
        }
        unset($slide);

        return $slides;
    }
}
