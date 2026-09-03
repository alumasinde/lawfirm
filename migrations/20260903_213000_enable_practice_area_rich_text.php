<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $statement = $pdo->prepare(
        'SELECT field_config_json FROM admin_resources WHERE resource_key = :resource_key LIMIT 1'
    );
    $statement->execute(['resource_key' => 'practice-areas']);
    $json = $statement->fetchColumn();

    $config = [];

    if (is_string($json) && $json !== '') {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $config = is_array($decoded) ? $decoded : [];
        } catch (JsonException) {
            $config = [];
        }
    }

    foreach (['body', 'approach_body', 'cta_body'] as $field) {
        $config[$field] = [
            ...((array) ($config[$field] ?? [])),
            'type' => 'richtext',
        ];
    }

    $update = $pdo->prepare(
        'UPDATE admin_resources
         SET field_config_json = :config
         WHERE resource_key = :resource_key'
    );

    $update->execute([
        'resource_key' => 'practice-areas',
        'config' => json_encode($config, JSON_THROW_ON_ERROR),
    ]);
};
