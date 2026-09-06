<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $columns = $pdo->query("SHOW COLUMNS FROM media LIKE 'checksum'")->fetchAll();

    if ($columns === []) {
        $pdo->exec('ALTER TABLE media ADD COLUMN checksum CHAR(64) NULL AFTER alt_text');
        $pdo->exec('CREATE INDEX media_checksum_index ON media (checksum)');
    }

    $root = dirname(__DIR__, 2);

    $rows = $pdo->query(
        'SELECT id, path FROM media WHERE checksum IS NULL OR checksum = ""'
    )->fetchAll(\PDO::FETCH_ASSOC);

    $update = $pdo->prepare(
        'UPDATE media SET checksum = :checksum WHERE id = :id'
    );

    foreach ($rows as $row) {
        $path = $root . '/public_html' . (string) $row['path'];

        if (!is_file($path)) {
            continue;
        }

        $checksum = hash_file('sha256', $path);

        if ($checksum === false) {
            continue;
        }

        $update->execute([
            'checksum' => $checksum,
            'id' => (int) $row['id'],
        ]);
    }
};
