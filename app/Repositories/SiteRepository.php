<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class SiteRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function content(array $keys): array
    {
        if ($keys === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $rows = $this->database->statement(
            'SELECT * FROM site_content WHERE is_enabled = 1 AND content_key IN (' . $placeholders . ')',
            $keys
        )->fetchAll();

        $result = [];

        foreach ($rows as $row) {
            $result[$row['content_key']] = $row;
        }

        return $result;
    }

    public function navigation(string $location): array
    {
        return $this->database->statement(
            'SELECT * FROM site_navigation WHERE location = :location AND is_enabled = 1 ORDER BY sort_order ASC, id ASC',
            ['location' => $location]
        )->fetchAll();
    }
}
