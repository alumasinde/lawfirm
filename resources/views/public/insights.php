<?php
$featured = $articles[0] ?? null;
$latest = array_slice($articles, 1);
$page = $page ?? null;
$more = $more ?? null;
$fallbackLabel = $fallback['title'] ?? '';
?>
<section class="page-hero page-hero--compact">
    <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($page['title'] ?? 'Insights & Updates', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page['body'])): ?><p class="lead"><?= htmlspecialchars($page['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<section class="page-section insights-page">
    <div class="container">
        <?php if ($featured): ?>
            <article class="featured-insight">
                <div class="featured-insight__media">
                    <?php if (!empty($featured['cover_path'])): ?>
                        <img src="<?= htmlspecialchars($featured['cover_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="eager">
                    <?php else: ?>
                        <div class="featured-insight__placeholder"><span><?= htmlspecialchars($fallbackLabel, ENT_QUOTES, 'UTF-8') ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="featured-insight__content">
                    <p class="section-label">Featured · <?= htmlspecialchars(date('d M Y', strtotime($featured['published_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                    <h2><a href="/insights/<?= rawurlencode($featured['slug']) ?>"><?= htmlspecialchars($featured['title'], ENT_QUOTES, 'UTF-8') ?></a></h2>
                    <?php if (!empty($featured['excerpt'])): ?><p class="lead"><?= htmlspecialchars($featured['excerpt'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <a class="text-link" href="/insights/<?= rawurlencode($featured['slug']) ?>">Read the full insight <span aria-hidden="true">→</span></a>
                </div>
            </article>
        <?php else: ?>
            <div class="content-empty"><p>No insights are currently available.</p></div>
        <?php endif; ?>

        <?php if ($latest !== []): ?>
            <div class="section-heading section-heading--compact">
                <div>
                    <?php if (!empty($more['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($more['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <h2><?= htmlspecialchars($more['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php if (!empty($more['body'])): ?><p><?= htmlspecialchars($more['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                </div>
            </div>
            <div class="card-grid card-grid--articles">
                <?php foreach ($latest as $article): ?>
                    <article class="article-card">
                        <a class="article-card__media" href="/insights/<?= rawurlencode($article['slug']) ?>" aria-label="Read <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (!empty($article['cover_path'])): ?><img src="<?= htmlspecialchars($article['cover_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" loading="lazy"><?php else: ?><span class="article-card__placeholder"><?= htmlspecialchars($fallbackLabel, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </a>
                        <div class="article-card__content">
                            <p class="section-label"><?= htmlspecialchars(date('d M Y', strtotime($article['published_at'])), ENT_QUOTES, 'UTF-8') ?></p>
                            <h3><a href="/insights/<?= rawurlencode($article['slug']) ?>"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                            <?php if (!empty($article['excerpt'])): ?><p><?= htmlspecialchars($article['excerpt'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <a class="article-card__link" href="/insights/<?= rawurlencode($article['slug']) ?>">Read insight <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
