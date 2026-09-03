<section class="admin-page">
    <div class="admin-page__intro practice-editor-header">
        <div>
            <p class="admin-kicker">Practice area details</p>
            <h1><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Shape this service page in a clear sequence: who handles it, what the firm has done, useful insights, and where visitors can go next.</p>
        </div>
        <div class="admin-page__actions">
            <a class="admin-view-site" href="/admin/content/practice-areas/<?= (int) $area['id'] ?>/edit">Edit overview</a>
            <a class="admin-view-site" href="/practice-areas/<?= rawurlencode($area['slug']) ?>" target="_blank" rel="noopener">View public page ↗</a>
        </div>
    </div>

    <?php if (!empty($message)): ?><div class="admin-notice" role="status">Practice area details were saved successfully.</div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <div class="practice-editor-summary">
        <div><strong><?= count($details['contacts']) ?></strong><span>Key contacts</span></div>
        <div><strong><?= count(array_filter($details['experience'])) ?></strong><span>Experience items</span></div>
        <div><strong><?= count($details['insights']) ?></strong><span>Related insights</span></div>
        <div><strong><?= count($details['related']) ?></strong><span>Related services</span></div>
    </div>

    <form class="admin-editor practice-detail-editor" method="post" action="/admin/practice-areas/<?= (int) $area['id'] ?>/details">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(AppCoreCsrf::token(), ENT_QUOTES, 'UTF-8') ?>">

        <section class="admin-detail-section" id="contacts">
            <div class="practice-editor-copy">
                <p class="admin-kicker">01 · Key contacts</p>
                <h2>Who should clients contact?</h2>
                <p>Select the advocates most relevant to this service. Their profile, role and email are pulled from the main Advocates database, so you only manage the relationship here.</p>
                <span class="practice-editor-tip">Best practice: feature only the advocates who actively handle this area.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head"><strong>Available advocates</strong><span><?= count($advocates) ?> total</span></div>
                <div class="admin-option-list">
                    <?php foreach ($advocates as $advocate): ?>
                        <?php $id = (int) $advocate['id']; $name = trim((string) $advocate['first_name'] . ' ' . $advocate['last_name']); ?>
                        <label>
                            <input type="checkbox" name="contacts[]" value="<?= $id ?>" <?= in_array($id, $details['contacts'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></strong><?php if (!empty($advocate['title'])): ?><small><?= htmlspecialchars($advocate['title'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="admin-detail-section" id="experience">
            <div class="practice-editor-copy">
                <p class="admin-kicker">02 · Experience</p>
                <h2>Show representative work</h2>
                <p>Add one matter, transaction, engagement or result per entry. Keep the language specific enough to demonstrate experience without exposing confidential client information.</p>
                <span class="practice-editor-tip">Use complete sentences and separate distinct matters into separate entries.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head"><strong>Representative experience</strong><span>Order is preserved</span></div>
                <div class="practice-experience-list" data-experience-list>
                    <?php foreach ($details['experience'] as $index => $item): ?>
                        <label class="admin-field admin-field--wide practice-experience-field">
                            <span>Experience <?= $index + 1 ?></span>
                            <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </label>
                    <?php endforeach; ?>
                    <?php if ($details['experience'] === []): ?>
                        <label class="admin-field admin-field--wide practice-experience-field">
                            <span>Experience 1</span>
                            <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"></textarea>
                        </label>
                    <?php endif; ?>
                </div>
                <template data-experience-template>
                    <label class="admin-field admin-field--wide practice-experience-field">
                        <span>Experience</span>
                        <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"></textarea>
                    </label>
                </template>
                <button class="admin-secondary-action" type="button" data-add-experience>+ Add another experience</button>
            </div>
        </section>

        <section class="admin-detail-section" id="insights">
            <div class="practice-editor-copy">
                <p class="admin-kicker">03 · Insights</p>
                <h2>Connect useful publications</h2>
                <p>Give visitors a direct path to articles and publications that help explain this service, demonstrate expertise, or cover current legal developments.</p>
                <span class="practice-editor-tip">Published articles are shown to visitors automatically when they are eligible.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head"><strong>Available insights</strong><span><?= count($articles) ?> in database</span></div>
                <div class="admin-option-list">
                    <?php foreach ($articles as $article): ?>
                        <?php $id = (int) $article['id']; ?>
                        <label>
                            <input type="checkbox" name="insights[]" value="<?= $id ?>" <?= in_array($id, $details['insights'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) ($article['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="admin-detail-section" id="related-services">
            <div class="practice-editor-copy">
                <p class="admin-kicker">04 · Related services</p>
                <h2>Guide visitors to the next relevant service</h2>
                <p>Connect this page to complementary Practice Areas. This creates a useful discovery path without duplicating content.</p>
                <span class="practice-editor-tip">Choose services that a client is realistically likely to need alongside this one.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head"><strong>Available practice areas</strong><span><?= count($areas) ?> other services</span></div>
                <div class="admin-option-list">
                    <?php foreach ($areas as $related): ?>
                        <?php $id = (int) $related['id']; ?>
                        <label>
                            <input type="checkbox" name="related[]" value="<?= $id ?>" <?= in_array($id, $details['related'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <div class="admin-editor__actions">
            <a href="/admin/content/practice-areas">Back to Practice Areas</a>
            <button type="submit">Save practice area details</button>
        </div>
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
