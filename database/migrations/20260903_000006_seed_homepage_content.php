<?php

declare(strict_types=1);

use PDO;

return function (PDO $pdo): void {
    $sections = [
        ['hero', 'Trusted legal counsel. Practical representation.', 'Webi Wenani & Associates Advocates', 'Professional legal services built around clear advice, careful preparation and committed representation.', 'Book a Consultation', '/contact', 'Explore Our Services', '/practice-areas', 1, 10],
        ['about', 'A modern approach to trusted legal service.', 'About the Firm', 'We help clients navigate important legal decisions with practical advice and professional representation.', 'Learn About Us', '/about', null, null, 1, 20],
        ['practice_areas', 'Legal support across the matters that matter.', 'Our Practice Areas', 'Explore the areas in which our team provides advice, representation and legal support.', 'View All Practice Areas', '/practice-areas', null, null, 1, 30],
        ['advocates', 'A team committed to your legal interests.', 'Our Advocates', 'Meet the professionals behind Webi Wenani & Associates Advocates.', 'Meet Our Team', '/advocates', null, null, 1, 40],
        ['insights', 'Legal knowledge and current perspectives.', 'Insights & Updates', 'Read practical perspectives, legal updates and firm insights.', 'View All Insights', '/insights', null, null, 1, 50],
        ['consultation', 'Let us discuss how we can assist you.', 'Need Legal Assistance?', 'Speak with our team and take the next step toward resolving your legal matter.', 'Request a Consultation', '/contact', 'Contact the Firm', '/contact', 1, 60]
    ];

    $statement = $pdo->prepare('INSERT INTO homepage_sections (section_key, title, eyebrow, body, primary_label, primary_url, secondary_label, secondary_url, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

    foreach ($sections as $section) {
        $statement->execute($section);
    }

    $slides = [
        ['Legal advice with clarity and confidence.', 'We provide practical legal guidance and committed representation for individuals, businesses and organisations.', 'Book a Consultation', '/contact', 'Our Practice Areas', '/practice-areas', 0.55, 1, 10],
        ['Professional representation when it matters most.', 'Our approach combines careful preparation, sound legal judgment and attention to every client matter.', 'Meet Our Advocates', '/advocates', 'Contact the Firm', '/contact', 0.60, 1, 20],
        ['Building trusted legal relationships.', 'We work to understand the issues, explain the options and pursue practical outcomes.', 'Learn About the Firm', '/about', 'Legal Insights', '/insights', 0.58, 1, 30]
    ];

    $statement = $pdo->prepare('INSERT INTO homepage_slides (title, body, primary_label, primary_url, secondary_label, secondary_url, overlay_opacity, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

    foreach ($slides as $slide) {
        $statement->execute($slide);
    }

    $practiceAreas = [
        ['Corporate & Commercial Law', 'corporate-commercial-law', 'Legal support for businesses, transactions and commercial relationships.', 'briefcase', 1, 1, 10],
        ['Litigation & Dispute Resolution', 'litigation-dispute-resolution', 'Representation and strategic advice in disputes and contested matters.', 'scale', 1, 1, 20],
        ['Employment & Labour Law', 'employment-labour-law', 'Guidance on workplace relationships, compliance and employment disputes.', 'people', 1, 1, 30],
        ['Property & Real Estate', 'property-real-estate', 'Support for property transactions, interests and related legal matters.', 'building', 1, 1, 40]
    ];

    $statement = $pdo->prepare('INSERT INTO practice_areas (name, slug, excerpt, icon, is_featured, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)');

    foreach ($practiceAreas as $area) {
        $statement->execute($area);
    }

    $advocates = [
        ['Webi', 'Wenani', 'webi-wenani', 'Advocate', 'Professional legal representation focused on practical advice, careful preparation and client confidence.', 1, 1, 10]
    ];

    $statement = $pdo->prepare('INSERT INTO advocates (first_name, last_name, slug, title, bio, is_featured, is_enabled, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

    foreach ($advocates as $advocate) {
        $statement->execute($advocate);
    }

    $articles = [
        ['Understanding your legal options before taking action', 'understanding-your-legal-options', 'A practical starting point when evaluating an important legal matter.', 'A legal issue often involves choices that benefit from early professional advice and careful preparation.', 1, 1, 1],
        ['Why clear agreements matter in business relationships', 'why-clear-agreements-matter', 'Clear legal agreements help define expectations and reduce avoidable disputes.', 'Well-prepared agreements create a stronger foundation for commercial relationships.', 1, 1, 1],
        ['Preparing for an effective legal consultation', 'preparing-for-an-effective-legal-consultation', 'A few simple preparations can help make an initial consultation more productive.', 'Bringing relevant information and questions helps establish a clearer understanding of the matter.', 1, 1, 1]
    ];

    $statement = $pdo->prepare('INSERT INTO articles (title, slug, excerpt, body, published_at, is_featured, is_enabled)
        VALUES (?, ?, ?, ?, NOW(), ?, ?)');

    foreach ($articles as $article) {
        $statement->execute($article);
    }
};
