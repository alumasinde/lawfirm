<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PublicRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function sections(array $keys): array
    {
        if ($keys === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $rows = $this->database->statement(
            'SELECT * FROM homepage_sections WHERE is_enabled = 1 AND section_key IN (' . $placeholders . ')',
            $keys
        )->fetchAll();

        $result = [];

        foreach ($rows as $row) {
            $result[$row['section_key']] = $row;
        }

        return $result;
    }

    public function practiceAreas(bool $featuredOnly = false): array
    {
        $sql = 'SELECT * FROM practice_areas WHERE is_enabled = 1';

        if ($featuredOnly) {
            $sql .= ' AND is_featured = 1';
        }

        $sql .= ' ORDER BY sort_order ASC, id ASC';

        return $this->database->statement($sql)->fetchAll();
    }

    public function practiceArea(string $slug): ?array
    {
        $row = $this->database->statement(
            'SELECT * FROM practice_areas WHERE slug = :slug AND is_enabled = 1 LIMIT 1',
            ['slug' => $slug]
        )->fetch();

        return $row ?: null;
    }

    public function advocates(): array
    {
        return $this->database->statement(
            'SELECT a.*, m.path AS photo_path
             FROM advocates a
             LEFT JOIN media m ON m.id = a.photo_media_id
             WHERE a.is_enabled = 1
             ORDER BY a.sort_order ASC, a.id ASC'
        )->fetchAll();
    }

    public function advocate(string $slug): ?array
    {
        $row = $this->database->statement(
            'SELECT a.*, m.path AS photo_path
             FROM advocates a
             LEFT JOIN media m ON m.id = a.photo_media_id
             WHERE a.slug = :slug AND a.is_enabled = 1
             LIMIT 1',
            ['slug' => $slug]
        )->fetch();

        return $row ?: null;
    }

    public function articles(): array
    {
        return $this->database->statement(
            'SELECT a.*, m.path AS cover_path
             FROM articles a
             LEFT JOIN media m ON m.id = a.cover_media_id
             WHERE a.is_enabled = 1
               AND a.status = "published"
               AND a.published_at IS NOT NULL
               AND a.published_at <= NOW()
             ORDER BY a.published_at DESC, a.id DESC'
        )->fetchAll();
    }

    public function article(string $slug): ?array
    {
        $row = $this->database->statement(
            'SELECT a.*, m.path AS cover_path
             FROM articles a
             LEFT JOIN media m ON m.id = a.cover_media_id
             WHERE a.slug = :slug
               AND a.is_enabled = 1
               AND a.status = "published"
               AND a.published_at IS NOT NULL
               AND a.published_at <= NOW()
             LIMIT 1',
            ['slug' => $slug]
        )->fetch();

        return $row ?: null;
    }

    public function faqs(): array
    {
        return $this->database->statement(
            'SELECT * FROM faqs WHERE is_enabled = 1 ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public function saveInquiry(array $data): void
    {
        $this->database->statement(
            'INSERT INTO contact_inquiries (name, email, phone, subject, message)
             VALUES (:name, :email, :phone, :subject, :message)',
            $data
        );
    }
}
