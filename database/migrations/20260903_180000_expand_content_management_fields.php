<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec('ALTER TABLE practice_areas
        ADD COLUMN meta_title VARCHAR(255) NULL AFTER icon,
        ADD COLUMN meta_description VARCHAR(500) NULL AFTER meta_title');

    $pdo->exec('ALTER TABLE advocates
        ADD COLUMN email VARCHAR(190) NULL AFTER title,
        ADD COLUMN phone VARCHAR(60) NULL AFTER email,
        ADD COLUMN qualifications LONGTEXT NULL AFTER bio,
        ADD COLUMN specialisations LONGTEXT NULL AFTER qualifications,
        ADD COLUMN meta_title VARCHAR(255) NULL AFTER photo_media_id,
        ADD COLUMN meta_description VARCHAR(500) NULL AFTER meta_title');

    $pdo->exec('ALTER TABLE articles
        ADD COLUMN author_name VARCHAR(190) NULL AFTER body,
        ADD COLUMN category VARCHAR(120) NULL AFTER author_name,
        ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT "published" AFTER published_at,
        ADD COLUMN meta_title VARCHAR(255) NULL AFTER cover_media_id,
        ADD COLUMN meta_description VARCHAR(500) NULL AFTER meta_title,
        ADD KEY articles_status_published (status, published_at)');
};
