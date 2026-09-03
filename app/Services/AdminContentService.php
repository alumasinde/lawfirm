<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AdminContentRepository;
use App\Support\HtmlSanitizer;
use InvalidArgumentException;

final class AdminContentService
{
    public function __construct(private readonly AdminContentRepository $repository)
    {
    }

    public function resources(): array
    {
        return array_map(function (array $resource): array {
            $resource['list_columns'] = $this->decodeColumns($resource['list_columns_json'] ?? '[]');
            $resource['field_config'] = $this->fieldConfig($resource);

            return $resource;
        }, $this->repository->resources());
    }

    public function resource(string $key): ?array
    {
        $resource = $this->repository->resource($key);

        if ($resource === null) {
            return null;
        }

        $resource['list_columns'] = $this->decodeColumns($resource['list_columns_json'] ?? '[]');
        $resource['field_config'] = $this->fieldConfig($resource);

        return $resource;
    }

    public function list(array $resource, int $page, string $search): array
    {
        $result = $this->repository->paginate($resource, max(1, $page), 20, trim($search));

        return [
            ...$result,
            'page' => max(1, $page),
            'per_page' => 20,
            'pages' => max(1, (int) ceil($result['total'] / 20)),
            'search' => trim($search),
        ];
    }

    public function fields(array $resource): array
    {
        return array_values(array_filter(
            $this->repository->columns($resource),
            static fn (array $column): bool => !in_array($column['Field'], ['id', 'created_at', 'updated_at'], true)
        ));
    }

    public function find(array $resource, int $id): ?array
    {
        return $this->repository->find($resource, $id);
    }

    public function create(array $resource, array $input): int
    {
        $data = $this->payload($resource, $input);
        $this->validateUnique($resource, $data);

        return $this->repository->insert($resource, $data);
    }

    public function update(array $resource, int $id, array $input): void
    {
        $data = $this->payload($resource, $input);
        $this->validateUnique($resource, $data, $id);
        $this->repository->update($resource, $id, $data);
    }

    public function delete(array $resource, int $id): void
    {
        $this->repository->delete($resource, $id);
    }

    private function payload(array $resource, array $input): array
    {
        $fields = $this->fields($resource);
        $data = [];

        foreach ($fields as $field) {
            $name = (string) $field['Field'];
            $type = strtolower((string) $field['Type']);
            $nullable = (string) $field['Null'] === 'YES';
            $value = $input[$name] ?? null;

            if ($this->isBoolean($type)) {
                $data[$name] = filter_var($input[$name] ?? '0', FILTER_VALIDATE_BOOL) ? 1 : 0;
                continue;
            }

            if (is_string($value)) {
                $value = trim($value);
            }

            $fieldConfig = is_array($resource['field_config'][$name] ?? null)
                ? $resource['field_config'][$name]
                : [];

            if (($fieldConfig['type'] ?? '') === 'richtext' && is_string($value)) {
                $value = HtmlSanitizer::sanitize($value);
            }

            if ($value === '' && $nullable) {
                $data[$name] = null;
                continue;
            }

            if ($value === '' && !$nullable && str_contains($type, 'text')) {
                $data[$name] = '';
                continue;
            }

            if ($name === 'slug' && $value === '') {
                $value = $this->slugSource($input);
                $value = $this->slugify($value);
            }

            if ($value === '' && !$nullable) {
                throw new InvalidArgumentException($this->label($name) . ' is required.');
            }

            if ($value === null) {
                continue;
            }

            if (preg_match('/int|decimal|float|double/', $type) === 1 && !is_numeric($value)) {
                throw new InvalidArgumentException($this->label($name) . ' must be a valid number.');
            }

            $data[$name] = $value;
        }

        if (array_key_exists('slug', $data) && $data['slug'] === '') {
            $data['slug'] = $this->slugify($this->slugSource($input));
        }

        return $data;
    }

    private function validateUnique(array $resource, array $data, ?int $exceptId = null): void
    {
        foreach ($this->repository->uniqueColumns($resource) as $column) {
            if (!array_key_exists($column, $data) || $data[$column] === null || $data[$column] === '') {
                continue;
            }

            if ($this->repository->existsValue($resource, $column, $data[$column], $exceptId)) {
                throw new InvalidArgumentException(
                    $this->label($column) . ' must be unique.'
                );
            }
        }
    }

    private function fieldConfig(array $resource): array
    {
        $config = $this->decodeConfig($resource['field_config_json'] ?? '{}');

        // Practice areas always support editorial formatting. This is enforced in
        // code as well as migrations so existing installations immediately receive
        // the rich editor even when their database has not yet run the config migration.
        if (($resource['resource_key'] ?? '') === 'practice-areas') {
            foreach (['excerpt', 'body', 'approach_body', 'cta_body'] as $field) {
                $existing = is_array($config[$field] ?? null) ? $config[$field] : [];
                $config[$field] = [
                    ...$existing,
                    'type' => 'richtext',
                ];
            }
        }

        return $config;
    }

    private function decodeColumns(string $json): array
    {
        try {
            $columns = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return is_array($columns) ? array_values(array_filter($columns, 'is_string')) : [];
    }

    private function decodeConfig(?string $json): array
    {
        if (!is_string($json) || $json === '') {
            return [];
        }

        try {
            $config = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return is_array($config) ? $config : [];
    }

    private function isBoolean(string $type): bool
    {
        return preg_match('/tinyint\(1\)|boolean|bool/', $type) === 1;
    }

    private function label(string $field): string
    {
        return ucwords(str_replace('_', ' ', $field));
    }

    private function slugSource(array $input): string
    {
        $source = trim((string) ($input['name'] ?? $input['title'] ?? ''));

        if ($source !== '') {
            return $source;
        }

        return trim((string) ($input['first_name'] ?? '') . ' ' . (string) ($input['last_name'] ?? ''));
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}
