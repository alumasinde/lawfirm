<section class="admin-page">
    <div class="admin-page__intro">
        <div>
            <p class="admin-kicker">Website management</p>
            <h1>Manage website content</h1>
            <p>Choose a content area to review, create, edit and publish information without changing application templates.</p>
        </div>
        <a class="admin-view-site" href="/" target="_blank" rel="noopener">View public website ↗</a>
    </div>

    <div class="admin-resource-grid">
        <?php foreach ($resources as $resource): ?>
            <article class="admin-resource-card">
                <p class="admin-kicker"><?= htmlspecialchars($resource['label'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars((string) ($resource['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                <a href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>">Manage <?= htmlspecialchars($resource['label'], ENT_QUOTES, 'UTF-8') ?> →</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
