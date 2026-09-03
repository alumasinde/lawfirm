<?php

declare(strict_types=1);

return function (\PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS site_content (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        content_key VARCHAR(120) NOT NULL,
        title VARCHAR(255) NULL,
        eyebrow VARCHAR(120) NULL,
        body LONGTEXT NULL,
        primary_label VARCHAR(120) NULL,
        primary_url VARCHAR(500) NULL,
        secondary_label VARCHAR(120) NULL,
        secondary_url VARCHAR(500) NULL,
        meta_title VARCHAR(255) NULL,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY site_content_key_unique (content_key),
        KEY site_content_enabled_order (is_enabled, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $pdo->exec('CREATE TABLE IF NOT EXISTS site_navigation (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        location VARCHAR(40) NOT NULL,
        label VARCHAR(120) NOT NULL,
        url VARCHAR(500) NOT NULL,
        is_enabled TINYINT(1) NOT NULL DEFAULT 1,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY site_navigation_location_order (location, is_enabled, sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    $content = [
        ['site_identity', 'Webi Wenani', '& Associates Advocates', 'Clear legal advice, careful preparation and committed professional representation.', null, null, null, null, 'Webi Wenani & Associates Advocates | Legal Services', 1, 10],
        ['top_bar', 'Professional legal services and representation', null, null, 'Contact the Firm', '/contact', null, null, null, 1, 20],
        ['practice_areas_page', 'Practice Areas', 'Legal Services', 'Focused legal support for individuals, businesses and organisations across matters that require clear advice and careful representation.', null, null, null, null, 'Practice Areas | Webi Wenani & Associates Advocates', 1, 30],
        ['insights_page', 'Insights & Updates', 'Knowledge', 'Practical legal information, updates and guidance to help you approach important decisions with greater clarity.', null, null, null, null, 'Insights & Updates | Webi Wenani & Associates Advocates', 1, 40],
        ['insights_more', 'More insights', 'Latest', 'Further practical perspectives and legal updates from the firm.', null, null, null, null, null, 1, 50],
        ['about_page', 'A modern approach to trusted legal service.', 'About the Firm', 'We help clients navigate important legal decisions with practical advice and professional representation.', null, null, null, null, 'About the Firm | Webi Wenani & Associates Advocates', 1, 60],
        ['advocates_page', 'Our Advocates', 'The Team', 'Meet the professionals committed to understanding your matter and providing practical legal guidance.', null, null, null, null, 'Our Advocates | Webi Wenani & Associates Advocates', 1, 70],
        ['faq_page', 'Frequently Asked Questions', 'Helpful Answers', 'Answers to common questions about contacting the firm and taking the next step with your legal matter.', null, null, null, null, 'Frequently Asked Questions | Webi Wenani & Associates Advocates', 1, 80],
        ['contact_page', 'Let us discuss how we can assist.', 'Contact the Firm', 'Send an enquiry and the firm can follow up using the contact details you provide.', null, null, null, null, 'Contact the Firm | Webi Wenani & Associates Advocates', 1, 90],
        ['contact_details', 'Contact details', null, 'Telephone, email and office details can be managed from the firm contact settings.', null, null, null, null, null, 1, 100],
        ['article_fallback', 'Legal Insights', null, null, null, null, null, null, null, 1, 110],
        ['footer_explore', 'Explore', null, null, null, null, null, null, null, 1, 120],
        ['footer_connect', 'Connect', null, null, null, null, null, null, null, 1, 130],
    ];

    $statement = $pdo->prepare('INSERT INTO site_content
        (content_key, title, eyebrow, body, primary_label, primary_url, secondary_label, secondary_url, meta_title, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            eyebrow = VALUES(eyebrow),
            body = VALUES(body),
            primary_label = VALUES(primary_label),
            primary_url = VALUES(primary_url),
            secondary_label = VALUES(secondary_label),
            secondary_url = VALUES(secondary_url),
            meta_title = VALUES(meta_title),
            is_enabled = VALUES(is_enabled),
            sort_order = VALUES(sort_order)');

    foreach ($content as $item) {
        $statement->execute($item);
    }

    $navigation = [
        ['main', 'Home', '/', 1, 10],
        ['main', 'About', '/about', 1, 20],
        ['main', 'Practice Areas', '/practice-areas', 1, 30],
        ['main', 'Advocates', '/advocates', 1, 40],
        ['main', 'Insights', '/insights', 1, 50],
        ['main', 'FAQ', '/faq', 1, 60],
        ['footer_explore', 'About the Firm', '/about', 1, 10],
        ['footer_explore', 'Practice Areas', '/practice-areas', 1, 20],
        ['footer_explore', 'Our Advocates', '/advocates', 1, 30],
        ['footer_connect', 'Insights & Updates', '/insights', 1, 10],
        ['footer_connect', 'Frequently Asked Questions', '/faq', 1, 20],
        ['footer_connect', 'Book a Consultation', '/contact', 1, 30],
    ];

    foreach ($navigation as $item) {
        $check = $pdo->prepare('SELECT id FROM site_navigation WHERE location = ? AND url = ? LIMIT 1');
        $check->execute([$item[0], $item[2]]);
        $id = $check->fetchColumn();

        if ($id !== false) {
            $update = $pdo->prepare('UPDATE site_navigation SET label = ?, is_enabled = ?, sort_order = ? WHERE id = ?');
            $update->execute([$item[1], $item[3], $item[4], $id]);
            continue;
        }

        $insert = $pdo->prepare('INSERT INTO site_navigation (location, label, url, is_enabled, sort_order) VALUES (?, ?, ?, ?, ?)');
        $insert->execute($item);
    }
};
