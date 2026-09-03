<?php $page = $page ?? null; ?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($page['title'] ?? 'Our Advocates', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page['body'])): ?><p class="lead"><?= htmlspecialchars($page['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<section class="page-section">
    <div class="container">
        <?php if ($advocates === []): ?>
            <div class="content-empty"><p>No advocate profiles are currently available.</p></div>
        <?php else: ?>
            <div class="card-grid card-grid--team">
                <?php foreach ($advocates as $advocate): ?>
                    <article class="advocate-card">
                        <a class="advocate-card__image" href="/advocates/<?= rawurlencode($advocate['slug']) ?>" aria-label="View <?= htmlspecialchars($advocate['first_name'].' '.$advocate['last_name'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (!empty($advocate['photo_path'])): ?><img src="<?= htmlspecialchars($advocate['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($advocate['first_name'].' '.$advocate['last_name'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy"><?php else: ?><span class="advocate-card__placeholder"><?= htmlspecialchars(strtoupper(substr($advocate['first_name'],0,1).substr($advocate['last_name'],0,1)), ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </a>
                        <div class="advocate-card__content">
                            <?php if (!empty($advocate['title'])): ?><p class="section-label"><?= htmlspecialchars($advocate['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <h2><a href="/advocates/<?= rawurlencode($advocate['slug']) ?>"><?= htmlspecialchars($advocate['first_name'].' '.$advocate['last_name'], ENT_QUOTES, 'UTF-8') ?></a></h2>
                            <?php if (!empty($advocate['bio'])): ?><p><?= htmlspecialchars($advocate['bio'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <a class="text-link" href="/advocates/<?= rawurlencode($advocate['slug']) ?>">View profile <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
