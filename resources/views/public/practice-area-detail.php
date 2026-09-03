<?php

declare(strict_types=1);

use App\Support\HtmlSanitizer;

$overviewHeading = trim((string) ($area['overview_heading'] ?? '')) ?: 'Strategic legal support for your business';
$approachHeading = trim((string) ($area['approach_heading'] ?? '')) ?: 'Practical, commercially minded counsel';
$ctaHeading = trim((string) ($area['cta_heading'] ?? '')) ?: 'Let us help you navigate the next step';
$hasApproach = trim((string) ($area['approach_body'] ?? '')) !== '';
$ctaBody = trim((string) ($area['cta_body'] ?? ''));
$overviewIntro = trim((string) ($area['overview_intro'] ?? ''));

$renderRich = static function (mixed $value): string {
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    if (strip_tags($value) === $value) {
        return nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }

    return HtmlSanitizer::sanitize($value);
};
?>

<section class="page-hero page-hero--compact practice-detail-hero">
    <div class="container">
        <a class="practice-detail-hero__back" href="/practice-areas">← Practice Areas</a>
        <p class="section-label">Practice Area</p>
        <h1><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($area['excerpt'])): ?>
            <div class="lead rich-content rich-content--excerpt"><?= $renderRich($area['excerpt']) ?></div>
        <?php endif; ?>
    </div>
</section>

<section class="page-section practice-detail">
    <div class="container practice-detail__grid">
        <main class="practice-detail__main">
            <section class="practice-detail__section practice-detail__overview">
                <div class="practice-detail__section-head">
                    <p class="section-label">Overview</p>
                    <h2><?= htmlspecialchars($overviewHeading, ENT_QUOTES, 'UTF-8') ?></h2>
                </div>

                <?php if ($overviewIntro !== ''): ?>
                    <p class="practice-detail__lead"><?= nl2br(htmlspecialchars($overviewIntro, ENT_QUOTES, 'UTF-8')) ?></p>
                <?php endif; ?>

                <div class="rich-content"><?= $renderRich($area['body'] ?? '') ?></div>
            </section>

            <?php if ($hasApproach): ?>
                <section class="practice-detail__section practice-approach">
                    <div class="practice-detail__section-head">
                        <p class="section-label">Our approach</p>
                        <h2><?= htmlspecialchars($approachHeading, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <div class="rich-content"><?= $renderRich($area['approach_body']) ?></div>
                </section>
            <?php endif; ?>

            <?php if ($details['experience'] !== []): ?>
                <section class="practice-detail__section">
                    <div class="practice-detail__section-head">
                        <p class="section-label">Experience</p>
                        <h2>Experience in this area</h2>
                        <p class="practice-detail__intro">Our experience includes matters such as:</p>
                    </div>

                    <ol class="practice-experience">
                        <?php foreach ($details['experience'] as $item): ?>
                            <li><?= nl2br(htmlspecialchars((string) $item['content'], ENT_QUOTES, 'UTF-8')) ?></li>
                        <?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>

            <?php if ($details['insights'] !== []): ?>
                <section class="practice-detail__section">
                    <div class="practice-detail__section-head">
                        <p class="section-label">Related insights</p>
                        <h2>Articles and publications</h2>
                        <p class="practice-detail__intro">Related legal commentary and practical guidance from our team.</p>
                    </div>

                    <div class="practice-insights">
                        <?php foreach ($details['insights'] as $article): ?>
                            <a href="/insights/<?= rawurlencode($article['slug']) ?>">
                                <span class="practice-insights__meta">
                                    <?= !empty($article['category']) ? htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8') : 'Insight' ?>
                                </span>
                                <strong><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small>Read insight <b aria-hidden="true">→</b></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="practice-detail__next-step">
                <div>
                    <p class="section-label">Speak with our team</p>
                    <h2><?= htmlspecialchars($ctaHeading, ENT_QUOTES, 'UTF-8') ?></h2>
                    <div class="practice-detail__cta-copy rich-content rich-content--light">
                        <?= $ctaBody !== '' ? $renderRich($ctaBody) : 'Every matter is different. Speak with our team about your circumstances, objectives and the legal support you require.' ?>
                    </div>
                </div>
                <a class="button button--primary" href="/contact">Discuss your matter</a>
            </section>
        </main>

        <aside class="practice-detail__sidebar">
            <?php if ($details['contacts'] !== []): ?>
                <section class="practice-contacts">
                    <p class="section-label">Key contacts</p>
                    <h2>Our team</h2>

                    <?php foreach ($details['contacts'] as $contact): ?>
                        <?php $name = trim((string) $contact['first_name'] . ' ' . (string) $contact['last_name']); ?>
                        <article class="practice-contact">
                            <?php if (!empty($contact['photo_path'])): ?>
                                <a class="practice-contact__photo" href="/advocates/<?= rawurlencode($contact['slug']) ?>" aria-label="View <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>'s profile">
                                    <img src="<?= htmlspecialchars($contact['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                </a>
                            <?php else: ?>
                                <span class="practice-contact__photo practice-contact__photo--placeholder" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr((string) $contact['first_name'], 0, 1) . substr((string) $contact['last_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <div class="practice-contact__content">
                                <h3><a href="/advocates/<?= rawurlencode($contact['slug']) ?>"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></a></h3>
                                <?php if (!empty($contact['title'])): ?><p><?= htmlspecialchars($contact['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                                <?php if (!empty($contact['email'])): ?><a class="practice-contact__email" href="mailto:<?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>">Email</a><?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?php if ($details['related'] !== []): ?>
                <section class="practice-related">
                    <p class="section-label">Related services</p>
                    <h2>You may also need</h2>
                    <div>
                        <?php foreach ($details['related'] as $related): ?>
                            <a href="/practice-areas/<?= rawurlencode($related['slug']) ?>">
                                <?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?>
                                <span aria-hidden="true">→</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="practice-detail__contact-card">
                <p class="section-label">Confidential enquiry</p>
                <p>Tell us briefly about the matter and our team can direct your enquiry to the appropriate person.</p>
                <a href="/contact">Contact the firm <span aria-hidden="true">→</span></a>
            </section>
        </aside>
    </div>
</section>
