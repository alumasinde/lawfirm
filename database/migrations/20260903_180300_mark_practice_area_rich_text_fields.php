<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $config = [
        'excerpt' => [
            'type' => 'richtext',
            'hint' => 'A concise introduction for practice area cards and the page hero.',
        ],
        'body' => [
            'type' => 'richtext',
            'hint' => 'Write the main practice area content. Use Enter for a new paragraph and Shift + Enter for a line break.',
        ],
        'approach_body' => [
            'type' => 'richtext',
            'hint' => 'Describe how the firm approaches matters in this practice area.',
        ],
        'cta_body' => [
            'type' => 'richtext',
            'hint' => 'Write the short call-to-action supporting the contact section.',
        ],
        'meta_description' => [
            'type' => 'textarea',
            'maxlength' => 500,
        ],
    ];

    $statement = $pdo->prepare(
        'UPDATE admin_resources
         SET field_config_json = :config
         WHERE resource_key = "practice-areas"'
    );

    $statement->execute([
        'config' => json_encode($config, JSON_THROW_ON_ERROR),
    ]);
};
