<section class="admin-page">
    <div class="admin-page__intro admin-page__intro--split">
        <div>
            <p class="admin-kicker">Public website</p>
            <h1>Homepage Builder</h1>
            <p>Manage the homepage content from one focused workspace.</p>
        </div>
        <a class="admin-secondary-action" href="/" target="_blank" rel="noopener">Preview homepage</a>
    </div>

    <?php if (!empty($message)): ?><div class="admin-notice" role="status">Homepage updated successfully.</div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div><p class="admin-kicker">Sections</p><h2>Homepage copy</h2></div>
        </div>
        <div class="homepage-builder-sections">
            <?php foreach ($sections as $section): ?>
                <details class="homepage-builder-card">
                    <summary>
                        <span><strong><?= htmlspecialchars($section['eyebrow'] ?: $section['section_key'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($section['section_key'], ENT_QUOTES, 'UTF-8') ?></small></span>
                        <span><?= (int) $section['is_enabled'] === 1 ? 'Live' : 'Hidden' ?></span>
                    </summary>
                    <form class="admin-content-form" method="post" action="/admin/homepage/sections/<?= (int) $section['id'] ?>">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars(AppCoreCsrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                        <label class="admin-field"><span>Eyebrow</span><input name="eyebrow" value="<?= htmlspecialchars((string) $section['eyebrow'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-field"><span>Title</span><input name="title" value="<?= htmlspecialchars((string) $section['title'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-field admin-field--wide"><span>Description</span><textarea name="body" rows="5"><?= htmlspecialchars((string) $section['body'], ENT_QUOTES, 'UTF-8') ?></textarea></label>
                        <label class="admin-field"><span>Primary button</span><input name="primary_label" value="<?= htmlspecialchars((string) $section['primary_label'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-field"><span>Primary URL</span><input name="primary_url" value="<?= htmlspecialchars((string) $section['primary_url'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-field"><span>Secondary button</span><input name="secondary_label" value="<?= htmlspecialchars((string) $section['secondary_label'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-field"><span>Secondary URL</span><input name="secondary_url" value="<?= htmlspecialchars((string) $section['secondary_url'], ENT_QUOTES, 'UTF-8') ?>"></label>
                        <label class="admin-check"><input type="checkbox" name="is_enabled" value="1" <?= (int) $section['is_enabled'] === 1 ? 'checked' : '' ?>> <span>Show this section</span></label>
                        <div class="admin-form-actions"><button type="submit">Save section</button></div>
                    </form>
                </details>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div><p class="admin-kicker">Hero</p><h2>Homepage slides</h2></div>
            <a href="/admin/media">Open Media Library</a>
        </div>

        <div class="homepage-slide-list">
            <?php foreach ($slides as $slide): ?>
                <details class="homepage-builder-card">
                    <summary>
                        <span><strong><?= htmlspecialchars($slide['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= (int) $slide['sort_order'] ?></small></span>
                        <span><?= (int) $slide['is_enabled'] === 1 ? 'Live' : 'Hidden' ?></span>
                    </summary>
                    <form class="admin-content-form" method="post" action="/admin/homepage/slides/<?= (int) $slide['id'] ?>">
                        <?php include __DIR__ . '/partials/homepage-slide-fields.php'; ?>
                        <div class="admin-form-actions">
                            <button type="submit">Save slide</button>
                        </div>
                    </form>
                    <form method="post" action="/admin/homepage/slides/<?= (int) $slide['id'] ?>/delete" class="admin-danger-form" onsubmit="return confirm('Delete this slide?');">
                        <input type="hidden" name="_token" value="<?= htmlspecialchars(AppCoreCsrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit">Delete slide</button>
                    </form>
                </details>
            <?php endforeach; ?>
        </div>

        <details class="homepage-builder-card homepage-builder-card--new">
            <summary><span><strong>Add slide</strong><small>Create another hero slide</small></span><span>+</span></summary>
            <form class="admin-content-form" method="post" action="/admin/homepage/slides">
                <?php $slide = ['title' => '', 'body' => '', 'media_id' => '', 'mobile_media_id' => '', 'primary_label' => '', 'primary_url' => '', 'secondary_label' => '', 'secondary_url' => '', 'overlay_opacity' => '0.55', 'is_enabled' => 1, 'sort_order' => $nextSortOrder]; include __DIR__ . '/partials/homepage-slide-fields.php'; ?>
                <div class="admin-form-actions"><button type="submit">Create slide</button></div>
            </form>
        </details>
    </section>
</section>
