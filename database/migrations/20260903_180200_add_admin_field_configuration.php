<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec(
        'ALTER TABLE admin_resources
         ADD COLUMN field_config_json JSON NULL AFTER list_columns_json'
    );

    $configs = [
        'practice-areas' => [
            'meta_description' => ['type' => 'textarea', 'maxlength' => 500],
        ],
        'advocates' => [
            'email' => ['type' => 'email'],
            'phone' => ['type' => 'tel'],
            'meta_description' => ['type' => 'textarea', 'maxlength' => 500],
        ],
        'insights' => [
            'status' => ['type' => 'select', 'options' => [
                'draft' => 'Draft',
                'published' => 'Published',
                'scheduled' => 'Scheduled',
            ]],
            'category' => ['type' => 'text', 'placeholder' => 'e.g. Corporate law'],
            'meta_description' => ['type' => 'textarea', 'maxlength' => 500],
        ],
    ];

    $statement = $pdo->prepare(
        'UPDATE admin_resources
         SET field_config_json = :config
         WHERE resource_key = :resource_key'
    );

    foreach ($configs as $key => $config) {
        $statement->execute([
            'resource_key' => $key,
            'config' => json_encode($config, JSON_THROW_ON_ERROR),
        ]);
    }
};
