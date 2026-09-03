<?php

declare(strict_types=1);

namespace AppServices;

use AppRepositoriesPublicRepository;

final class PublicService
{
    public function __construct(private readonly PublicRepository $repository)
    {
    }

    public function sections(array $keys): array { return $this->repository->sections($keys); }
    public function practiceAreas(): array { return $this->repository->practiceAreas(); }
    public function practiceArea(string $slug): ?array { return $this->repository->practiceArea($slug); }
    public function advocates(): array { return $this->repository->advocates(); }
    public function advocate(string $slug): ?array { return $this->repository->advocate($slug); }
    public function articles(): array { return $this->repository->articles(); }
    public function article(string $slug): ?array { return $this->repository->article($slug); }
    public function faqs(): array { return $this->repository->faqs(); }

    public function inquiry(array $input): array
    {
        $data = [
            'name' => trim((string) ($input['name'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'phone' => trim((string) ($input['phone'] ?? '')),
            'subject' => trim((string) ($input['subject'] ?? '')),
            'message' => trim((string) ($input['message'] ?? '')),
        ];

        if ($data['name'] === '' || $data['message'] === '') {
            throw new InvalidArgumentException('Please provide your name and enquiry message.');
        }

        if ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Please provide a valid email address.');
        }

        $this->repository->saveInquiry($data);
        return $data;
    }
}
