<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS admin_resources (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        resource_key VARCHAR(80) NOT NULL,
        label VARCHAR(120) NOT NULL,
        description VARCHAR(255) NULL,
        table_name VARCHAR(120) NOT NULL,
        list_columns_json JSON NOT NULL,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY admin_resources_key_unique (resource_key),
        UNIQUE KEY admin_resources_table_unique (table_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $resources = [
        ['practice-areas', 'Practice Areas', 'Manage the legal services displayed on the public website.', 'practice_areas', ['name', 'slug', 'is_featured', 'is_enabled', 'sort_order'], 10],
        ['advocates', 'Advocates', 'Manage advocate profiles and public team information.', 'advocates', ['first_name', 'last_name', 'slug', 'position', 'is_featured', 'is_enabled', 'sort_order'], 20],
        ['insights', 'Insights', 'Manage legal articles, publication dates and visibility.', 'articles', ['title', 'slug', 'published_at', 'is_featured', 'is_enabled'], 30],
        ['faqs', 'Frequently Asked Questions', 'Manage the questions and answers available to visitors.', 'faqs', ['question', 'is_enabled', 'sort_order'], 40],
        ['site-content', 'Site Content', 'Manage reusable website copy and page metadata.', 'site_content', ['content_key', 'meta_title', 'is_enabled'], 50],
        ['navigation', 'Navigation', 'Manage public navigation labels, destinations and ordering.', 'site_navigation', ['location', 'label', 'url', 'is_enabled', 'sort_order'], 60],
        ['homepage-sections', 'Homepage Sections', 'Manage homepage section content and visibility.', 'homepage_sections', ['section_key', 'title', 'is_enabled', 'sort_order'], 70],
        ['homepage-slides', 'Homepage Slides', 'Manage homepage hero slides and ordering.', 'homepage_slides', ['eyebrow', 'title', 'is_enabled', 'sort_order'], 80],
        ['enquiries', 'Enquiries', 'Review, update and manage client contact enquiries.', 'contact_inquiries', ['name', 'email', 'phone', 'subject', 'status', 'created_at'], 90],
    ];

    $statement = $pdo->prepare(
        'INSERT INTO admin_resources
            (resource_key, label, description, table_name, list_columns_json, is_enabled, sort_order)
         VALUES
            (:resource_key, :label, :description, :table_name, :list_columns_json, 1, :sort_order)
         ON DUPLICATE KEY UPDATE
            label = VALUES(label),
            description = VALUES(description),
            table_name = VALUES(table_name),
            list_columns_json = VALUES(list_columns_json),
            sort_order = VALUES(sort_order)'
    );

    foreach ($resources as [$key, $label, $description, $table, $columns, $sortOrder]) {
        $statement->execute([
            'resource_key' => $key,
            'label' => $label,
            'description' => $description,
            'table_name' => $table,
            'list_columns_json' => json_encode($columns, JSON_THROW_ON_ERROR),
            'sort_order' => $sortOrder,
        ]);
    }
};
