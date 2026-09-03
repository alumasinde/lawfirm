<section class="admin-page">
    <div class="admin-page__intro practice-editor-header">
        <div>
            <p class="admin-kicker">Practice area · Page relationships</p>
            <h1><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Use this workspace to connect the supporting content around the main service page: the right advocates, representative experience, relevant publications and related services.</p>
        </div>
        <div class="admin-page__actions">
            <a class="admin-view-site" href="/admin/content/practice-areas/<?= (int) $area['id'] ?>/edit">← Edit page content</a>
            <a class="admin-view-site" href="/practice-areas/<?= rawurlencode($area['slug']) ?>" target="_blank" rel="noopener">Preview public page ↗</a>
        </div>
    </div>

    <?php if (!empty($message)): ?><div class="admin-notice" role="status">Practice area details were saved successfully.</div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

    <div class="practice-editor-summary" aria-label="Current page details">
        <div><strong data-summary-count="contacts"><?= count($details['contacts']) ?></strong><span>Key contacts</span></div>
        <div><strong data-summary-count="experience"><?= count(array_filter($details['experience'])) ?></strong><span>Experience items</span></div>
        <div><strong data-summary-count="insights"><?= count($details['insights']) ?></strong><span>Related insights</span></div>
        <div><strong data-summary-count="related"><?= count($details['related']) ?></strong><span>Related services</span></div>
    </div>

    <div class="practice-editor-path" aria-label="Details editor sections">
        <a href="#contacts"><span>01</span> Contacts</a>
        <a href="#experience"><span>02</span> Experience</a>
        <a href="#insights"><span>03</span> Insights</a>
        <a href="#related-services"><span>04</span> Related services</a>
    </div>

    <form class="admin-editor practice-detail-editor" method="post" action="/admin/practice-areas/<?= (int) $area['id'] ?>/details">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\\App\\Core\\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">

        <section class="admin-detail-section" id="contacts" data-detail-section="contacts">
            <div class="practice-editor-copy">
                <p class="admin-kicker">01 · Key contacts</p>
                <h2>Who should clients contact?</h2>
                <p>Select advocates who actively handle this service. Their profile, role and email remain managed in the Advocates area.</p>
                <span class="practice-editor-tip">Keep this focused. The strongest service pages usually show the most relevant people rather than everyone in the firm.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head">
                    <div><strong>Available advocates</strong><span><?= count($advocates) ?> available</span></div>
                    <div class="practice-selection-actions">
                        <button type="button" data-select-group="contacts">Select all</button>
                        <button type="button" data-clear-group="contacts">Clear</button>
                    </div>
                </div>
                <div class="admin-option-list" data-option-group="contacts">
                    <?php foreach ($advocates as $advocate): ?>
                        <?php $id = (int) $advocate['id']; $name = trim((string) $advocate['first_name'] . ' ' . $advocate['last_name']); ?>
                        <label class="admin-option-card">
                            <input type="checkbox" name="contacts[]" value="<?= $id ?>" <?= in_array($id, $details['contacts'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></strong><?php if (!empty($advocate['title'])): ?><small><?= htmlspecialchars($advocate['title'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="admin-detail-section" id="experience" data-detail-section="experience">
            <div class="practice-editor-copy">
                <p class="admin-kicker">02 · Experience</p>
                <h2>Show representative work</h2>
                <p>Add one matter, transaction, engagement or result per entry. The order here is the order visitors see on the public page.</p>
                <span class="practice-editor-tip">Describe the work clearly without exposing confidential client information.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head"><div><strong>Representative experience</strong><span>Each entry becomes one public item</span></div></div>
                <div class="practice-experience-list" data-experience-list>
                    <?php foreach ($details['experience'] as $index => $item): ?>
                        <div class="practice-experience-field" data-experience-item>
                            <div class="practice-experience-field__head">
                                <strong data-experience-number>Experience <?= $index + 1 ?></strong>
                                <button type="button" data-remove-experience>Remove</button>
                            </div>
                            <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($details['experience'] === []): ?>
                        <div class="practice-experience-field" data-experience-item>
                            <div class="practice-experience-field__head">
                                <strong data-experience-number>Experience 1</strong>
                                <button type="button" data-remove-experience>Remove</button>
                            </div>
                            <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"></textarea>
                        </div>
                    <?php endif; ?>
                </div>
                <template data-experience-template>
                    <div class="practice-experience-field" data-experience-item>
                        <div class="practice-experience-field__head">
                            <strong data-experience-number>Experience</strong>
                            <button type="button" data-remove-experience>Remove</button>
                        </div>
                        <textarea name="experience[]" rows="5" placeholder="Describe the matter, transaction or advisory work…"></textarea>
                    </div>
                </template>
                <button class="admin-secondary-action" type="button" data-add-experience>+ Add another experience</button>
            </div>
        </section>

        <section class="admin-detail-section" id="insights" data-detail-section="insights">
            <div class="practice-editor-copy">
                <p class="admin-kicker">03 · Insights</p>
                <h2>Connect useful publications</h2>
                <p>Give visitors a direct path to articles that explain this service, demonstrate expertise or cover relevant legal developments.</p>
                <span class="practice-editor-tip">Only choose publications that genuinely add value to this practice area.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head">
                    <div><strong>Available insights</strong><span><?= count($articles) ?> in database</span></div>
                    <div class="practice-selection-actions">
                        <button type="button" data-select-group="insights">Select all</button>
                        <button type="button" data-clear-group="insights">Clear</button>
                    </div>
                </div>
                <div class="admin-option-list" data-option-group="insights">
                    <?php foreach ($articles as $article): ?>
                        <?php $id = (int) $article['id']; ?>
                        <label class="admin-option-card">
                            <input type="checkbox" name="insights[]" value="<?= $id ?>" <?= in_array($id, $details['insights'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars((string) ($article['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="admin-detail-section" id="related-services" data-detail-section="related">
            <div class="practice-editor-copy">
                <p class="admin-kicker">04 · Related services</p>
                <h2>Guide visitors to the next relevant service</h2>
                <p>Connect complementary practice areas to create a useful path for clients without duplicating the same information.</p>
                <span class="practice-editor-tip">Choose services a client is realistically likely to need alongside this one.</span>
            </div>
            <div class="practice-editor-control">
                <div class="practice-editor-control__head">
                    <div><strong>Available practice areas</strong><span><?= count($areas) ?> other services</span></div>
                    <div class="practice-selection-actions">
                        <button type="button" data-select-group="related">Select all</button>
                        <button type="button" data-clear-group="related">Clear</button>
                    </div>
                </div>
                <div class="admin-option-list" data-option-group="related">
                    <?php foreach ($areas as $related): ?>
                        <?php $id = (int) $related['id']; ?>
                        <label class="admin-option-card">
                            <input type="checkbox" name="related[]" value="<?= $id ?>" <?= in_array($id, $details['related'], true) ? 'checked' : '' ?>>
                            <span><strong><?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <div class="admin-editor__actions practice-detail-editor__actions">
            <a href="/admin/content/practice-areas">← Back to Practice Areas</a>
            <button type="submit">Save page details</button>
        </div>
    </form>
</section>

<script>
(function () {
    const updateExperienceNumbers = function (form) {
        const items = form.querySelectorAll('[data-experience-item]');
        items.forEach(function (item, index) {
            const label = item.querySelector('[data-experience-number]');
            if (label) label.textContent = 'Experience ' + (index + 1);
        });

        const count = Array.from(items).filter(function (item) {
            const input = item.querySelector('textarea');
            return input && input.value.trim() !== '';
        }).length;
        const summary = document.querySelector('[data-summary-count="experience"]');
        if (summary) summary.textContent = String(count);
    };

    const updateGroupCount = function (group) {
        const container = document.querySelector('[data-option-group="' + group + '"]');
        const summary = document.querySelector('[data-summary-count="' + group + '"]');
        if (!container || !summary) return;
        summary.textContent = String(container.querySelectorAll('input[type="checkbox"]:checked').length);
    };

    document.addEventListener('click', function (event) {
        const addButton = event.target.closest('[data-add-experience]');
        if (addButton) {
            const form = addButton.closest('form');
            const list = form.querySelector('[data-experience-list]');
            const template = form.querySelector('[data-experience-template]');
            if (list && template) {
                list.appendChild(template.content.cloneNode(true));
                updateExperienceNumbers(form);
                list.lastElementChild.querySelector('textarea').focus();
            }
            return;
        }

        const removeButton = event.target.closest('[data-remove-experience]');
        if (removeButton) {
            const form = removeButton.closest('form');
            const item = removeButton.closest('[data-experience-item]');
            const list = form.querySelector('[data-experience-list]');
            if (item && list) {
                if (list.querySelectorAll('[data-experience-item]').length === 1) {
                    const input = item.querySelector('textarea');
                    if (input) input.value = '';
                } else {
                    item.remove();
                }
                updateExperienceNumbers(form);
            }
            return;
        }

        const selectButton = event.target.closest('[data-select-group]');
        const clearButton = event.target.closest('[data-clear-group]');
        const button = selectButton || clearButton;
        if (button) {
            const group = button.dataset.selectGroup || button.dataset.clearGroup;
            const container = document.querySelector('[data-option-group="' + group + '"]');
            if (container) {
                container.querySelectorAll('input[type="checkbox"]').forEach(function (input) {
                    input.checked = Boolean(selectButton);
                });
                updateGroupCount(group);
            }
        }
    });

    document.addEventListener('change', function (event) {
        if (event.target.matches('[data-option-group] input[type="checkbox"]')) {
            const container = event.target.closest('[data-option-group]');
            if (container) updateGroupCount(container.dataset.optionGroup);
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target.matches('[data-experience-item] textarea')) {
            const form = event.target.closest('form');
            if (form) updateExperienceNumbers(form);
        }
    });
})();
</script>