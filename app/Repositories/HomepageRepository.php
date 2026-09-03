<?php

declare(strict_types=1);

namespace AppRepositories;

use AppCoreDatabase;

final class HomepageRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function section(string $key): ?array
    {
        $statement = $this->database->statement(
            'SELECT * FROM homepage_sections WHERE section_key = :section_key AND is_enabled = 1 LIMIT 1',
            ['section_key' => $key]
        );

        return $statement->fetch() ?: null;
    }

    public function slides(): array
    {
        return $this->database->statement(
            'SELECT s.*, desktop.path AS image_path, mobile.path AS mobile_image_path
             FROM homepage_slides s
             LEFT JOIN media desktop ON desktop.id = s.media_id
             LEFT JOIN media mobile ON mobile.id = s.mobile_media_id
             WHERE s.is_enabled = 1
             ORDER BY s.sort_order ASC, s.id ASC'
        )->fetchAll();
    }

    public function practiceAreas(int $limit): array
    {
        return $this->database->statement(
            'SELECT * FROM practice_areas
             WHERE is_enabled = 1 AND is_featured = 1
             ORDER BY sort_order ASC, id ASC
             LIMIT ' . max(1, $limit)
        )->fetchAll();
    }

    public function advocates(int $limit): array
    {
        return $this->database->statement(
            'SELECT a.*, m.path AS photo_path
             FROM advocates a
             LEFT JOIN media m ON m.id = a.photo_media_id
             WHERE a.is_enabled = 1 AND a.is_featured = 1
             ORDER BY a.sort_order ASC, a.id ASC
             LIMIT ' . max(1, $limit)
        )->fetchAll();
    }

    public function articles(int $limit): array
    {
        return $this->database->statement(
            'SELECT a.*, m.path AS cover_path
             FROM articles a
             LEFT JOIN media m ON m.id = a.cover_media_id
             WHERE a.is_enabled = 1 AND a.is_featured = 1 AND a.published_at IS NOT NULL
             ORDER BY a.published_at DESC, a.id DESC
             LIMIT ' . max(1, $limit)
        )->fetchAll();
    }
}
