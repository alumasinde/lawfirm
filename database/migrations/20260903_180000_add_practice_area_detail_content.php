<?php

declare(strict_types=1);

return static function (PDO $pdo): void {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS practice_area_contacts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            practice_area_id BIGINT UNSIGNED NOT NULL,
            advocate_id BIGINT UNSIGNED NOT NULL,
            sort_order INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY practice_area_contacts_unique (practice_area_id, advocate_id),
            KEY practice_area_contacts_order (practice_area_id, sort_order, id),
            CONSTRAINT fk_practice_area_contacts_area FOREIGN KEY (practice_area_id) REFERENCES practice_areas(id) ON DELETE CASCADE,
            CONSTRAINT fk_practice_area_contacts_advocate FOREIGN KEY (advocate_id) REFERENCES advocates(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS practice_area_experience (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            practice_area_id BIGINT UNSIGNED NOT NULL,
            content TEXT NOT NULL,
            sort_order INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY practice_area_experience_order (practice_area_id, sort_order, id),
            CONSTRAINT fk_practice_area_experience_area FOREIGN KEY (practice_area_id) REFERENCES practice_areas(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS practice_area_insights (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            practice_area_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NOT NULL,
            sort_order INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY practice_area_insights_unique (practice_area_id, article_id),
            KEY practice_area_insights_order (practice_area_id, sort_order, id),
            CONSTRAINT fk_practice_area_insights_area FOREIGN KEY (practice_area_id) REFERENCES practice_areas(id) ON DELETE CASCADE,
            CONSTRAINT fk_practice_area_insights_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS practice_area_related (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            practice_area_id BIGINT UNSIGNED NOT NULL,
            related_practice_area_id BIGINT UNSIGNED NOT NULL,
            sort_order INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY practice_area_related_unique (practice_area_id, related_practice_area_id),
            KEY practice_area_related_order (practice_area_id, sort_order, id),
            CONSTRAINT fk_practice_area_related_area FOREIGN KEY (practice_area_id) REFERENCES practice_areas(id) ON DELETE CASCADE,
            CONSTRAINT fk_practice_area_related_target FOREIGN KEY (related_practice_area_id) REFERENCES practice_areas(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
};
