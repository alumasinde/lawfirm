<?php
$hero = $sections['hero'] ?? null;
$about = $sections['about'] ?? null;
$practice = $sections['practice_areas'] ?? null;
$team = $sections['advocates'] ?? null;
$insights = $sections['insights'] ?? null;
$consultation = $sections['consultation'] ?? null;
?>

<?php if ($hero !== null): ?>
<section class="hero" data-carousel>
    <div class="hero__slides">
        <?php foreach ($slides as $index => $slide): ?>
            <article class="hero__slide<?= $index === 0 ? ' is-active' : '' ?>" data-slide aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>">
                <?php if (!empty($slide['image_path'])): ?>
                    <picture class="hero__media">
                        <?php if (!empty($slide['mobile_image_path'])): ?>
                            <source media="(max-width: 760px)" srcset="<?= htmlspecialchars($slide['mobile_image_path'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                        <img src="<?= htmlspecialchars($slide['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                    </picture>
                <?php else: ?>
                    <div class="hero__fallback" aria-hidden="true"></div>
                <?php endif; ?>
                <div class="hero__overlay" style="--hero-overlay: <?= htmlspecialchars((string) $slide['overlay_opacity'], ENT_QUOTES, 'UTF-8') ?>"></div>
                <div class="hero__content container">
                    <p class="section-label section-label--light"><?= htmlspecialchars($hero['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    <h1><?= htmlspecialchars($slide['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                    <?php if (!empty($slide['body'])): ?><p class="hero__body"><?= htmlspecialchars($slide['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <div class="hero__actions">
                        <?php if (!empty($slide['primary_label']) && !empty($slide['primary_url'])): ?>
                            <?php $variant = 'secondary'; $href = $slide['primary_url']; $label = $slide['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
                        <?php endif; ?>
                        <?php if (!empty($slide['secondary_label']) && !empty($slide['secondary_url'])): ?>
                            <?php $variant = 'light-outline'; $href = $slide['secondary_url']; $label = $slide['secondary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (count($slides) > 1): ?>
        <div class="hero__controls container">
            <div class="hero__dots" role="tablist" aria-label="Homepage highlights">
                <?php foreach ($slides as $index => $slide): ?>
                    <button type="button" class="hero__dot<?= $index === 0 ? ' is-active' : '' ?>" data-slide-trigger="<?= $index ?>" aria-label="Show slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="hero__arrows">
                <button type="button" class="hero__arrow" data-carousel-prev aria-label="Previous slide">←</button>
                <button type="button" class="hero__arrow" data-carousel-next aria-label="Next slide">→</button>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>

<?php if ($about !== null): ?>
<section class="page-section">
    <div class="container split-section">
        <div>
            <p class="section-label"><?= htmlspecialchars($about['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($about['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
        </div>
        <div>
            <p class="lead"><?= htmlspecialchars($about['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($about['primary_label']) && !empty($about['primary_url'])): ?>
                <?php $variant = 'outline'; $href = $about['primary_url']; $label = $about['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($practice !== null && $practiceAreas !== []): ?>
<section class="page-section section--subtle">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="section-label"><?= htmlspecialchars($practice['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <h2><?= htmlspecialchars($practice['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <?php if (!empty($practice['primary_label']) && !empty($practice['primary_url'])): ?>
                <?php $variant = 'outline'; $href = $practice['primary_url']; $label = $practice['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
        </div>
        <div class="card-grid card-grid--practice">
            <?php foreach ($practiceAreas as $area): ?>
                <article class="card practice-card">
                    <span class="practice-card__number"><?= str_pad((string) $area['id'], 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($area['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    <a href="/practice-areas/<?= rawurlencode($area['slug']) ?>">Learn more <span aria-hidden="true">→</span></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($team !== null && $advocates !== []): ?>
<section class="page-section">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="section-label"><?= htmlspecialchars($team['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <h2><?= htmlspecialchars($team['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <?php if (!empty($team['primary_label']) && !empty($team['primary_url'])): ?>
                <?php $variant = 'outline'; $href = $team['primary_url']; $label = $team['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
        </div>
        <div class="card-grid card-grid--team">
            <?php foreach ($advocates as $advocate): ?>
                <article class="advocate-card">
                    <div class="advocate-card__image">
                        <?php if (!empty($advocate['photo_path'])): ?>
                            <img src="<?= htmlspecialchars($advocate['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($advocate['first_name'] . ' ' . $advocate['last_name'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php else: ?>
                            <div class="advocate-card__placeholder"><?= htmlspecialchars(strtoupper(substr($advocate['first_name'], 0, 1) . substr($advocate['last_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="advocate-card__content">
                        <p class="section-label"><?= htmlspecialchars($advocate['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <h3><?= htmlspecialchars($advocate['first_name'] . ' ' . $advocate['last_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($advocate['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($insights !== null && $articles !== []): ?>
<section class="page-section section--subtle">
    <div class="container">
        <div class="section-heading">
            <div>
                <p class="section-label"><?= htmlspecialchars($insights['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <h2><?= htmlspecialchars($insights['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <?php if (!empty($insights['primary_label']) && !empty($insights['primary_url'])): ?>
                <?php $variant = 'outline'; $href = $insights['primary_url']; $label = $insights['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
        </div>
        <div class="card-grid card-grid--articles">
            <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <?php if (!empty($article['cover_path'])): ?>
                        <img src="<?= htmlspecialchars($article['cover_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                    <?php else: ?>
                        <div class="article-card__placeholder"></div>
                    <?php endif; ?>
                    <div class="article-card__content">
                        <p class="section-label"><?= !empty($article['published_at']) ? htmlspecialchars(date('d M Y', strtotime($article['published_at'])), ENT_QUOTES, 'UTF-8') : '' ?></p>
                        <h3><a href="/insights/<?= rawurlencode($article['slug']) ?>"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></a></h3>
                        <p><?= htmlspecialchars($article['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($consultation !== null): ?>
<section class="consultation-cta">
    <div class="container consultation-cta__inner">
        <div>
            <p class="section-label section-label--light"><?= htmlspecialchars($consultation['eyebrow'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <h2><?= htmlspecialchars($consultation['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <p><?= htmlspecialchars($consultation['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="hero__actions">
            <?php if (!empty($consultation['primary_label']) && !empty($consultation['primary_url'])): ?>
                <?php $variant = 'secondary'; $href = $consultation['primary_url']; $label = $consultation['primary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
            <?php if (!empty($consultation['secondary_label']) && !empty($consultation['secondary_url'])): ?>
                <?php $variant = 'light-outline'; $href = $consultation['secondary_url']; $label = $consultation['secondary_label']; require BASE_PATH . '/resources/views/components/button.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
