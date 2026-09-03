<?php

declare(strict_types=1);

use App\Core\Database;

$app = require __DIR__ . '/app/bootstrap.php';
$database = $app->database();
$pdo = $database->pdo();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS migrations (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        batch INT UNSIGNED NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
$files = glob(__DIR__ . '/database/migrations/*.php') ?: [];
sort($files, SORT_STRING);

$pending = array_filter($files, function (string $file) use ($applied): bool {
    return !in_array(basename($file), $applied, true);
});

if ($pending === []) {
    echo "Nothing to migrate.\n";
    exit(0);
}

$batch = (int) $pdo->query('SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations')->fetchColumn();

foreach ($pending as $file) {
    $migration = require $file;

    if (!is_callable($migration)) {
        throw new RuntimeException('Migration must return a callable: ' . basename($file));
    }

    $name = basename($file);

    $pdo->beginTransaction();

    try {
        $migration($pdo);

        $statement = $pdo->prepare(
            'INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)'
        );

        $statement->execute([
            'migration' => $name,
            'batch' => $batch,
        ]);

        $pdo->commit();
        echo "Migrated: {$name}\n";
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}
