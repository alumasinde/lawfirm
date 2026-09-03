<?php $page = $page ?? null; ?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($page['title'] ?? 'Frequently Asked Questions', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page['body'])): ?><p class="lead"><?= htmlspecialchars($page['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<section class="page-section">
    <div class="container faq-list">
        <?php if ($faqs === []): ?><div class="content-empty"><p>No frequently asked questions are currently available.</p></div><?php endif; ?>
        <?php foreach ($faqs as $faq): ?>
            <details>
                <summary><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary>
                <p><?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')) ?></p>
            </details>
        <?php endforeach; ?>
    </div>
</section>
