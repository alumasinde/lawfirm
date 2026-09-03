<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS migrations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT UNSIGNED NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
};
