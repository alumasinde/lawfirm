<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $statement = $pdo->prepare(
        'UPDATE admin_resources
         SET list_columns_json = :columns
         WHERE resource_key = :resource_key'
    );

    $resources = [
        'practice-areas' => ['name', 'slug', 'is_featured', 'is_enabled', 'sort_order'],
        'advocates' => ['first_name', 'last_name', 'title', 'is_featured', 'is_enabled', 'sort_order'],
        'insights' => ['title', 'status', 'category', 'published_at', 'is_featured', 'is_enabled'],
    ];

    foreach ($resources as $key => $columns) {
        $statement->execute([
            'resource_key' => $key,
            'columns' => json_encode($columns, JSON_THROW_ON_ERROR),
        ]);
    }
};
