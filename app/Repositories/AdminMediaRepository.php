<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AdminMediaRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function paginate(int $page, int $perPage, string $search): array
    {
        $where = '';
        $params = [];

        if ($search !== '') {
            $where = ' WHERE filename LIKE :search OR alt_text LIKE :search OR mime_type LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $total = (int) $this->database->statement(
            'SELECT COUNT(*) FROM media' . $where,
            $params
        )->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $rows = $this->database->statement(
            'SELECT * FROM media' . $where . ' ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
            $params
        )->fetchAll();

        return ['rows' => $rows, 'total' => $total];
    }

    public function options(int $limit = 200): array
    {
        return $this->database->statement(
            'SELECT id, path, filename, alt_text, mime_type FROM media ORDER BY id DESC LIMIT ' . max(1, $limit)
        )->fetchAll();
    }

    public function insert(array $data): int
    {
        $this->database->statement(
            'INSERT INTO media (disk, path, filename, mime_type, size_bytes, width, height, alt_text)
             VALUES (:disk, :path, :filename, :mime_type, :size_bytes, :width, :height, :alt_text)',
            $data
        );

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $row = $this->database->statement(
            'SELECT * FROM media WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function usages(int $id): array
    {
        $references = $this->database->statement(
            'SELECT TABLE_NAME, COLUMN_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND REFERENCED_TABLE_NAME = "media"
               AND REFERENCED_COLUMN_NAME = "id"'
        )->fetchAll();

        $usage = [];

        foreach ($references as $reference) {
            $table = $this->identifier((string) $reference['TABLE_NAME']);
            $column = $this->identifier((string) $reference['COLUMN_NAME']);
            $count = (int) $this->database->statement(
                'SELECT COUNT(*) FROM `' . $table . '` WHERE `' . $column . '` = :id',
                ['id' => $id]
            )->fetchColumn();

            if ($count > 0) {
                $usage[] = [
                    'table' => $table,
                    'column' => $column,
                    'count' => $count,
                ];
            }
        }

        return $usage;
    }

    public function delete(int $id): void
    {
        $this->database->statement(
            'DELETE FROM media WHERE id = :id',
            ['id' => $id]
        );
    }

    private function identifier(string $value): string
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) !== 1) {
            throw new \InvalidArgumentException('Invalid database identifier.');
        }

        return $value;
    }
}
