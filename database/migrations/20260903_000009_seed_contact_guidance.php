<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $rows = [
        ['contact_guidance_response', 'Response', null, 'Provide your preferred contact details in your enquiry.', 1, 10],
        ['contact_guidance_services', 'Legal Services', null, 'Explore the firm\'s practice areas before sending your enquiry.', 1, 20],
        ['contact_guidance_privacy', 'Privacy', null, 'Please avoid sending highly sensitive documents through the initial contact form.', 1, 30],
    ];

    $statement = $pdo->prepare('INSERT INTO site_content (content_key, title, eyebrow, body, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE title = VALUES(title), eyebrow = VALUES(eyebrow), body = VALUES(body), is_enabled = VALUES(is_enabled), sort_order = VALUES(sort_order)');

    foreach ($rows as $row) {
        $statement->execute($row);
    }
};
