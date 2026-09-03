<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS faqs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(500) NOT NULL,
        answer LONGTEXT NOT NULL,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY faqs_enabled_order (is_enabled, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS contact_inquiries (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        email VARCHAR(190) NULL,
        phone VARCHAR(50) NULL,
        subject VARCHAR(255) NULL,
        message LONGTEXT NOT NULL,
        status VARCHAR(30) NOT NULL DEFAULT "new",
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY contact_inquiries_status_created (status, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $statement = $pdo->prepare('INSERT INTO faqs (question, answer, sort_order) VALUES (?, ?, ?)');
    foreach ([
        ['How do I request a consultation?', 'You can contact the firm using the enquiry form, telephone or the available contact channels.', 10],
        ['Do you handle corporate and commercial matters?', 'The firm practice areas are managed through the website and provide an overview of the legal services currently offered.', 20],
        ['Can I submit an enquiry online?', 'Yes. You can send an initial enquiry through the contact page and the firm can follow up using the details you provide.', 30],
    ] as $faq) {
        $statement->execute($faq);
    }
};
