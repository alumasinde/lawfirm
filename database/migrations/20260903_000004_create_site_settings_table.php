<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS site_settings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(150) NOT NULL,
            setting_value LONGTEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY site_settings_key_unique (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
};
