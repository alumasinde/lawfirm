<article class="page-section">
    <div class="container profile-detail">
        <div class="profile-detail__media">
            <?php if (!empty($advocate['photo_path'])): ?>
                <img src="<?= htmlspecialchars($advocate['photo_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($advocate['name'], ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div class="advocate-card__placeholder"><?= htmlspecialchars(strtoupper(substr($advocate['first_name'], 0, 1) . substr($advocate['last_name'], 0, 1)), ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
        <div>
            <p class="section-label"><?= htmlspecialchars($advocate['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <h1><?= htmlspecialchars($advocate['name'], ENT_QUOTES, 'UTF-8') ?></h1>

            <?php if (!empty($advocate['email']) || !empty($advocate['phone'])): ?>
                <div class="profile-detail__contact">
                    <?php if (!empty($advocate['email'])): ?><a href="mailto:<?= htmlspecialchars($advocate['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($advocate['email'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
                    <?php if (!empty($advocate['phone'])): ?><a href="tel:<?= htmlspecialchars($advocate['phone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($advocate['phone'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="rich-content"><?= nl2br(htmlspecialchars($advocate['bio'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>

            <?php if (!empty($advocate['qualifications'])): ?>
                <section class="profile-detail__section">
                    <h2>Qualifications</h2>
                    <div class="rich-content"><?= nl2br(htmlspecialchars($advocate['qualifications'], ENT_QUOTES, 'UTF-8')) ?></div>
                </section>
            <?php endif; ?>

            <?php if (!empty($advocate['specialisations'])): ?>
                <section class="profile-detail__section">
                    <h2>Specialisations</h2>
                    <div class="rich-content"><?= nl2br(htmlspecialchars($advocate['specialisations'], ENT_QUOTES, 'UTF-8')) ?></div>
                </section>
            <?php endif; ?>
        </div>
    </div>
</article>
