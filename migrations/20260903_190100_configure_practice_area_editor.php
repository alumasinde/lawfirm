<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $configs = [
        'practice-areas' => [
            'excerpt' => ['type' => 'textarea', 'maxlength' => 500, 'placeholder' => 'One strong sentence explaining the service…', 'hint' => 'Keep this concise. It appears near the top of the public page.'],
            'overview_intro' => ['type' => 'textarea', 'maxlength' => 1200, 'placeholder' => 'Opening paragraph for the overview…', 'hint' => 'Use a short lead paragraph to orient visitors.'],
            'overview_heading' => ['type' => 'text', 'placeholder' => 'Strategic legal support for your business'],
            'body' => ['type' => 'textarea', 'maxlength' => 12000, 'placeholder' => 'Write the main overview. Separate paragraphs with a blank line…', 'hint' => 'Use a blank line between paragraphs. Avoid very long blocks of text.'],
            'approach_heading' => ['type' => 'text', 'placeholder' => 'How we help clients'],
            'approach_body' => ['type' => 'textarea', 'maxlength' => 3000, 'placeholder' => 'Explain the firm’s approach, priorities or practical value…', 'hint' => 'Use this for the “how we help” story rather than repeating the overview.'],
            'cta_heading' => ['type' => 'text', 'placeholder' => 'Let us help you navigate the next step'],
            'cta_body' => ['type' => 'textarea', 'maxlength' => 1200, 'placeholder' => 'A concise invitation to contact the firm…'],
            'meta_description' => ['type' => 'textarea', 'maxlength' => 500, 'hint' => 'SEO description. Aim for a clear, useful summary of the service.'],
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
