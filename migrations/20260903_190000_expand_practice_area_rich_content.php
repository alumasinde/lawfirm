<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $columns = $pdo->query('SHOW COLUMNS FROM practice_areas')->fetchAll(PDO::FETCH_COLUMN);

    $definitions = [
        'overview_intro' => 'TEXT NULL AFTER excerpt',
        'overview_heading' => 'VARCHAR(255) NULL AFTER overview_intro',
        'approach_heading' => 'VARCHAR(255) NULL AFTER overview_heading',
        'approach_body' => 'TEXT NULL AFTER approach_heading',
        'cta_heading' => 'VARCHAR(255) NULL AFTER approach_body',
        'cta_body' => 'TEXT NULL AFTER cta_heading',
    ];

    foreach ($definitions as $column => $definition) {
        if (!in_array($column, $columns, true)) {
            $pdo->exec('ALTER TABLE practice_areas ADD COLUMN `' . $column . '` ' . $definition);
        }
    }
};
