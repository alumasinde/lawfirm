<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class AdminContentRepository
{
    public function __construct(private readonly Database $database)
    {
    }

    public function resources(): array
    {
        return $this->database->statement(
            'SELECT resource_key, label, description, table_name, list_columns_json, field_config_json, sort_order
             FROM admin_resources
             WHERE is_enabled = 1
             ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public function resource(string $key): ?array
    {
        $row = $this->database->statement(
            'SELECT resource_key, label, description, table_name, list_columns_json, field_config_json
             FROM admin_resources
             WHERE resource_key = :resource_key AND is_enabled = 1
             LIMIT 1',
            ['resource_key' => $key]
        )->fetch();

        return $row ?: null;
    }

    public function columns(array $resource): array
    {
        $table = $this->identifier((string) $resource['table_name']);
        $rows = $this->database->statement('SHOW COLUMNS FROM `' . $table . '`')->fetchAll();

        return array_values(array_filter($rows, static fn (array $column): bool => $column['Field'] !== 'password_hash'));
    }

    public function paginate(array $resource, int $page, int $perPage, string $search): array
    {
        $table = $this->identifier((string) $resource['table_name']);
        $columns = $this->columns($resource);
        $searchable = array_values(array_filter($columns, static function (array $column): bool {
            return preg_match('/char|text|json/i', (string) $column['Type']) === 1;
        }));

        $where = '';
        $params = [];

        if ($search !== '' && $searchable !== []) {
            $parts = [];
            foreach ($searchable as $index => $column) {
                $name = $this->identifier((string) $column['Field']);
                $param = 'search_' . $index;
                $parts[] = '`' . $name . '` LIKE :' . $param;
                $params[$param] = '%' . $search . '%';
            }
            $where = ' WHERE ' . implode(' OR ', $parts);
        }

        $count = (int) $this->database->statement(
            'SELECT COUNT(*) FROM `' . $table . '`' . $where,
            $params
        )->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $rows = $this->database->statement(
            'SELECT * FROM `' . $table . '`' . $where . ' ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
            $params
        )->fetchAll();

        return ['rows' => $rows, 'total' => $count, 'columns' => $columns];
    }

    public function uniqueColumns(array $resource): array
    {
        $table = $this->identifier((string) $resource['table_name']);
        $rows = $this->database->statement('SHOW INDEX FROM `' . $table . '` WHERE Non_unique = 0')->fetchAll();
        $groups = [];

        foreach ($rows as $row) {
            $key = (string) $row['Key_name'];
            $groups[$key][] = (string) $row['Column_name'];
        }

        $columns = [];

        foreach ($groups as $key => $group) {
            if ($key !== 'PRIMARY' && count($group) === 1) {
                $columns[] = $group[0];
            }
        }

        return array_values(array_unique($columns));
    }

    public function existsValue(array $resource, string $column, mixed $value, ?int $exceptId = null): bool
    {
        $table = $this->identifier((string) $resource['table_name']);
        $column = $this->identifier($column);
        $sql = 'SELECT 1 FROM `' . $table . '` WHERE `' . $column . '` = :value';
        $params = ['value' => $value];

        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $exceptId;
        }

        $sql .= ' LIMIT 1';

        return $this->database->statement($sql, $params)->fetchColumn() !== false;
    }

    public function find(array $resource, int $id): ?array
    {
        $table = $this->identifier((string) $resource['table_name']);
        $row = $this->database->statement(
            'SELECT * FROM `' . $table . '` WHERE id = :id LIMIT 1',
            ['id' => $id]
        )->fetch();

        return $row ?: null;
    }

    public function insert(array $resource, array $data): int
    {
        $table = $this->identifier((string) $resource['table_name']);
        $fields = array_keys($data);

        if ($fields === []) {
            throw new \InvalidArgumentException('No values were provided.');
        }

        $columns = array_map(fn (string $field): string => '`' . $this->identifier($field) . '`', $fields);
        $placeholders = array_map(fn (string $field): string => ':' . $field, $fields);

        $this->database->statement(
            'INSERT INTO `' . $table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')',
            $data
        );

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function update(array $resource, int $id, array $data): void
    {
        $table = $this->identifier((string) $resource['table_name']);

        if ($data === []) {
            return;
        }

        $assignments = [];
        foreach (array_keys($data) as $field) {
            $assignments[] = '`' . $this->identifier($field) . '` = :' . $field;
        }

        $data['id'] = $id;

        $this->database->statement(
            'UPDATE `' . $table . '` SET ' . implode(', ', $assignments) . ' WHERE id = :id',
            $data
        );
    }

    public function delete(array $resource, int $id): void
    {
        $table = $this->identifier((string) $resource['table_name']);

        $this->database->statement(
            'DELETE FROM `' . $table . '` WHERE id = :id',
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
