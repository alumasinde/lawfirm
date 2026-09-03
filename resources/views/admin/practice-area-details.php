<section class="admin-page">
    <div class="admin-page__intro">
        <div>
            <p class="admin-kicker">Practice area details</p>
            <h1><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Build the public practice page from database relationships. Contacts, experience, insights and related services are managed independently for this practice area.</p>
        </div>
        <div class="admin-page__actions">
            <a class="admin-view-site" href="/admin/content/practice-areas/<?= (int) $area['id'] ?>/edit">Edit overview</a>
            <a class="admin-view-site" href="/practice-areas/<?= rawurlencode($area['slug']) ?>" target="_blank" rel="noopener">View public page ↗</a>
        </div>
    </div>

    <?php if (!empty($message)): ?><div class="admin-notice" role="status">Practice area details were saved successfully.</div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <form class="admin-editor practice-detail-editor" method="post" action="/admin/practice-areas/<?= (int) $area['id'] ?>/details">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(AppCoreCsrf::token(), ENT_QUOTES, 'UTF-8') ?>">

        <section class="admin-detail-section">
            <div><p class="admin-kicker">Key contacts</p><h2>Who should clients contact?</h2><p>Select advocates. Their public profile, role and email are fetched dynamically from the database.</p></div>
            <div class="admin-option-list">
                <?php foreach ($advocates as $advocate): ?>
                    <?php $id = (int) $advocate['id']; $name = trim((string) $advocate['first_name'] . ' ' . (string) $advocate['last_name']); ?>
                    <label><input type="checkbox" name="contacts[]" value="<?= $id ?>" <?= in_array($id, $details['contacts'], true) ? 'checked' : '' ?>><span><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></strong><?php if (!empty($advocate['title'])): ?><small><?= htmlspecialchars($advocate['title'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?></span></label>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="admin-detail-section">
            <div><p class="admin-kicker">Experience</p><h2>Recent representative experience</h2><p>Add each matter separately. Empty entries are ignored and the display order is preserved.</p></div>
            <div class="practice-experience-list" data-experience-list>
                <?php foreach ($details['experience'] as $index => $item): ?>
                    <label class="admin-field admin-field--wide"><span>Experience <?= $index + 1 ?></span><textarea name="experience[]" rows="5"><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></textarea></label>
                <?php endforeach; ?>
                <?php if ($details['experience'] === []): ?><label class="admin-field admin-field--wide"><span>Experience 1</span><textarea name="experience[]" rows="5"></textarea></label><?php endif; ?>
            </div>
            <template data-experience-template><label class="admin-field admin-field--wide"><span>Experience</span><textarea name="experience[]" rows="5"></textarea></label></template>
            <button class="admin-secondary-action" type="button" data-add-experience>Add another experience</button>
        </section>

        <section class="admin-detail-section">
            <div><p class="admin-kicker">Recent insights</p><h2>Related publications</h2><p>Select published articles from the existing Insights database.</p></div>
            <div class="admin-option-list">
                <?php foreach ($articles as $article): ?>
                    <?php $id = (int) $article['id']; ?>
                    <label><input type="checkbox" name="insights[]" value="<?= $id ?>" <?= in_array($id, $details['insights'], true) ? 'checked' : '' ?>><span><strong><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) ($article['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></span></label>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="admin-detail-section">
            <div><p class="admin-kicker">Related services</p><h2>Connect other practice areas</h2><p>These links are pulled from your existing Practice Areas and remain dynamic when names or slugs change.</p></div>
            <div class="admin-option-list">
                <?php foreach ($areas as $related): ?>
                    <?php $id = (int) $related['id']; ?>
                    <label><input type="checkbox" name="related[]" value="<?= $id ?>" <?= in_array($id, $details['related'], true) ? 'checked' : '' ?>><span><strong><?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?></strong></span></label>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="admin-editor__actions"><a href="/admin/content/practice-areas">Back to Practice Areas</a><button type="submit">Save practice area details</button></div>
    </form>
</section>
<script>
document.addEventListener('click', function (event) {
    const button = event.target.closest('[data-add-experience]');
    if (!button) return;
    const form = button.closest('form');
    const list = form.querySelector('[data-experience-list]');
    const template = form.querySelector('[data-experience-template]');
    if (list && template) list.appendChild(template.content.cloneNode(true));
});
</script>
