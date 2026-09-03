<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AdminHomepageRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function sections(): array
    {
        return $this->database->statement(
            'SELECT * FROM homepage_sections ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public function slides(): array
    {
        return $this->database->statement(
            'SELECT s.*, m.path AS image_path, mm.path AS mobile_image_path
             FROM homepage_slides s
             LEFT JOIN media m ON m.id = s.media_id
             LEFT JOIN media mm ON mm.id = s.mobile_media_id
             ORDER BY s.sort_order ASC, s.id ASC'
        )->fetchAll();
    }

    public function updateSection(int $id, array $data): void
    {
        $this->database->statement(
            'UPDATE homepage_sections SET
                title = :title,
                eyebrow = :eyebrow,
                body = :body,
                primary_label = :primary_label,
                primary_url = :primary_url,
                secondary_label = :secondary_label,
                secondary_url = :secondary_url,
                is_enabled = :is_enabled
             WHERE id = :id',
            [...$data, 'id' => $id]
        );
    }

    public function createSlide(array $data): int
    {
        $this->database->statement(
            'INSERT INTO homepage_slides
                (title, body, media_id, mobile_media_id, primary_label, primary_url, secondary_label, secondary_url, overlay_opacity, is_enabled, sort_order)
             VALUES
                (:title, :body, :media_id, :mobile_media_id, :primary_label, :primary_url, :secondary_label, :secondary_url, :overlay_opacity, :is_enabled, :sort_order)',
            $data
        );

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function updateSlide(int $id, array $data): void
    {
        $this->database->statement(
            'UPDATE homepage_slides SET
                title = :title,
                body = :body,
                media_id = :media_id,
                mobile_media_id = :mobile_media_id,
                primary_label = :primary_label,
                primary_url = :primary_url,
                secondary_label = :secondary_label,
                secondary_url = :secondary_url,
                overlay_opacity = :overlay_opacity,
                is_enabled = :is_enabled,
                sort_order = :sort_order
             WHERE id = :id',
            [...$data, 'id' => $id]
        );
    }

    public function deleteSlide(int $id): void
    {
        $this->database->statement('DELETE FROM homepage_slides WHERE id = :id', ['id' => $id]);
    }

    public function nextSortOrder(): int
    {
        return (int) $this->database->statement(
            'SELECT COALESCE(MAX(sort_order), 0) + 10 FROM homepage_slides'
        )->fetchColumn();
    }
}
