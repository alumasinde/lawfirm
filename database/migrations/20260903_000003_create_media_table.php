<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS media (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            disk VARCHAR(50) NOT NULL DEFAULT "public",
            path VARCHAR(500) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(100) NOT NULL,
            size_bytes BIGINT UNSIGNED NOT NULL,
            width INT UNSIGNED NULL,
            height INT UNSIGNED NULL,
            alt_text VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY media_path_unique (path)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
};
