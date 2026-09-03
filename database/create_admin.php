<?php

declare(strict_types=1);

$rootPath = dirname(__DIR__);
$app = require $rootPath . '/app/bootstrap.php';
$pdo = $app->database()->pdo();

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

[$script, $firstName, $lastName, $email, $password] = array_pad($argv, 5, null);

if (!$firstName || !$lastName || !$email || !$password) {
    fwrite(STDERR, "Usage: php database/create_admin.php FirstName LastName email@example.com StrongPassword\n");
    exit(1);
}

$email = strtolower(trim($email));
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($password) < 12) {
    fwrite(STDERR, "Use a valid email and a password of at least 12 characters.\n");
    exit(1);
}

$pdo->beginTransaction();

try {
    $statement = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, password_hash, status)
         VALUES (:first_name, :last_name, :email, :password_hash, "active")
         ON DUPLICATE KEY UPDATE
             first_name = VALUES(first_name),
             last_name = VALUES(last_name),
             password_hash = VALUES(password_hash),
             status = "active"'
    );
    $statement->execute([
        'first_name' => trim($firstName),
        'last_name' => trim($lastName),
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    $lookup = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $lookup->execute([$email]);
    $userId = (int) $lookup->fetchColumn();

    $roleId = (int) $pdo->query('SELECT id FROM roles WHERE name = "admin" LIMIT 1')->fetchColumn();
    if ($roleId < 1) {
        throw new RuntimeException('Administrator role not found. Run migrations first.');
    }

    $assign = $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)');
    $assign->execute([$userId, $roleId]);

    $pdo->commit();
    echo "Administrator account is ready for {$email}.\n";
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
