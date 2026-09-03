<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS homepage_sections (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        section_key VARCHAR(100) NOT NULL,
        title VARCHAR(255) NULL,
        eyebrow VARCHAR(120) NULL,
        body LONGTEXT NULL,
        primary_label VARCHAR(120) NULL,
        primary_url VARCHAR(500) NULL,
        secondary_label VARCHAR(120) NULL,
        secondary_url VARCHAR(500) NULL,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY homepage_sections_key_unique (section_key),
        KEY homepage_sections_enabled_order (is_enabled, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS homepage_slides (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        body TEXT NULL,
        media_id BIGINT UNSIGNED NULL,
        mobile_media_id BIGINT UNSIGNED NULL,
        primary_label VARCHAR(120) NULL,
        primary_url VARCHAR(500) NULL,
        secondary_label VARCHAR(120) NULL,
        secondary_url VARCHAR(500) NULL,
        overlay_opacity DECIMAL(3,2) NOT NULL DEFAULT 0.55,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY homepage_slides_enabled_order (is_enabled, sort_order),
        CONSTRAINT homepage_slides_media_fk FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL,
        CONSTRAINT homepage_slides_mobile_media_fk FOREIGN KEY (mobile_media_id) REFERENCES media(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS practice_areas (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        slug VARCHAR(190) NOT NULL,
        excerpt TEXT NULL,
        body LONGTEXT NULL,
        icon VARCHAR(100) NULL,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY practice_areas_slug_unique (slug),
        KEY practice_areas_featured_order (is_enabled, is_featured, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS advocates (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        slug VARCHAR(190) NOT NULL,
        title VARCHAR(180) NULL,
        bio LONGTEXT NULL,
        photo_media_id BIGINT UNSIGNED NULL,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY advocates_slug_unique (slug),
        KEY advocates_featured_order (is_enabled, is_featured, sort_order),
        CONSTRAINT advocates_photo_fk FOREIGN KEY (photo_media_id) REFERENCES media(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS articles (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(190) NOT NULL,
        excerpt TEXT NULL,
        body LONGTEXT NULL,
        cover_media_id BIGINT UNSIGNED NULL,
        published_at DATETIME NULL,
        is_featured TINYINT(1) NOT NULL DEFAULT 0,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY articles_slug_unique (slug),
        KEY articles_featured_published (is_enabled, is_featured, published_at),
        CONSTRAINT articles_cover_fk FOREIGN KEY (cover_media_id) REFERENCES media(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
};
