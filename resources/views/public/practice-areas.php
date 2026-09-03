<?php

declare(strict_types=1);

use App\Support\HtmlSanitizer;

$page = $page ?? null;

$renderExcerpt = static function (mixed $value): string {
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    if (strip_tags($value) === $value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    return HtmlSanitizer::sanitize($value);
};
?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($page['title'] ?? 'Practice Areas', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page['body'])): ?><p class="lead"><?= htmlspecialchars($page['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<section class="page-section">
    <div class="container">
        <?php if ($areas === []): ?>
            <div class="content-empty"><p>No practice areas are currently available.</p></div>
        <?php else: ?>
            <div class="card-grid card-grid--practice">
                <?php foreach ($areas as $area): ?>
                    <a class="card practice-card practice-card--interactive" href="/practice-areas/<?= rawurlencode($area['slug']) ?>">
                        <div class="practice-card__top">
                            <?php if (!empty($area['icon'])): ?><span class="practice-card__icon"><?= htmlspecialchars($area['icon'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                            <span class="practice-card__arrow" aria-hidden="true">↗</span>
                        </div>
                        <h2><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <?php if (!empty($area['excerpt'])): ?><div class="practice-card__excerpt"><?= $renderExcerpt($area['excerpt']) ?></div><?php endif; ?>
                        <span class="practice-card__link">Learn more <span aria-hidden="true">→</span></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
