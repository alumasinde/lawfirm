<?php $hero = $page ?? $section ?? null; ?>
<?php if ($hero): ?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($hero['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($hero['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($hero['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($hero['body'])): ?><p class="lead"><?= htmlspecialchars($hero['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<?php endif; ?>
