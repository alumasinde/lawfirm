<section class="admin-page">
    <div class="admin-page__intro">
        <div>
            <p class="admin-kicker">Content assets</p>
            <h1>Media Library</h1>
            <p>Upload and reuse images across advocate profiles, insights and homepage content.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-notice" role="status">Media library updated successfully.</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <section class="admin-panel admin-media-upload">
        <div class="admin-panel__header">
            <div>
                <p class="admin-kicker">Upload</p>
                <h2>Add an image</h2>
            </div>
        </div>
        <form class="admin-upload-form" method="post" action="/admin/media" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
            <label class="admin-field">
                <span>Image</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" required>
            </label>
            <label class="admin-field">
                <span>Alternative text</span>
                <input type="text" name="alt_text" maxlength="255" placeholder="Describe the image for accessibility">
            </label>
            <button type="submit">Upload image</button>
        </form>
    </section>

    <section class="admin-panel">
        <form class="admin-search" method="get" action="/admin/media">
            <input type="search" name="q" value="<?= htmlspecialchars($listing['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search uploaded images">
            <button type="submit">Search</button>
            <?php if ($listing['search'] !== ''): ?><a href="/admin/media">Clear</a><?php endif; ?>
        </form>

        <?php if ($listing['rows'] === []): ?>
            <div class="admin-empty-state">
                <h2>No media found</h2>
                <p>Upload your first image to start building a reusable media library.</p>
            </div>
        <?php else: ?>
            <div class="admin-media-grid">
                <?php foreach ($listing['rows'] as $media): ?>
                    <article class="admin-media-card">
                        <a href="<?= htmlspecialchars($media['path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="admin-media-card__preview">
                            <img src="<?= htmlspecialchars($media['path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($media['alt_text'] ?: $media['filename'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </a>
                        <div class="admin-media-card__body">
                            <strong title="<?= htmlspecialchars($media['filename'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($media['filename'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= htmlspecialchars($media['mime_type'], ENT_QUOTES, 'UTF-8') ?> · <?= number_format(((int) $media['size_bytes']) / 1024, 1) ?> KB</span>
                            <?php if (!empty($media['alt_text'])): ?><small><?= htmlspecialchars($media['alt_text'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
                            <?php if ((int) ($media['usage_count'] ?? 0) > 0): ?>
                                <small>In use <?= (int) $media['usage_count'] ?> time(s)</small>
                            <?php else: ?>
                                <small>Not currently in use</small>
                            <?php endif; ?>
                            <form method="post" action="/admin/media/<?= (int) $media['id'] ?>/delete" onsubmit="return confirm('Delete this image? Images that are in use cannot be deleted.');">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($listing['pages'] > 1): ?>
                <nav class="admin-pagination" aria-label="Media pagination">
                    <?php for ($page = 1; $page <= $listing['pages']; $page++): ?>
                        <a class="<?= $page === $listing['page'] ? 'is-active' : '' ?>" href="/admin/media?page=<?= $page ?>&q=<?= rawurlencode($listing['search']) ?>"><?= $page ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</section>
