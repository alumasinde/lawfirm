<section class="admin-page">
    <div class="admin-page__intro">
        <div>
            <p class="admin-kicker">Content editor</p>
            <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars((string) ($resource['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a class="admin-view-site" href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>">Back to list</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form class="admin-editor" method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-editor__grid">
            <?php foreach ($fields as $field): ?>
                <?php
                $name = (string) $field['Field'];
                $type = strtolower((string) $field['Type']);
                $value = $record[$name] ?? '';
                $label = ucwords(str_replace('_', ' ', $name));
                $isBoolean = preg_match('/tinyint\(1\)|boolean|bool/', $type) === 1;
                $isText = preg_match('/text|json/', $type) === 1;
                $isDateTime = str_contains($type, 'datetime') || str_contains($type, 'timestamp');
                $isMedia = str_ends_with($name, '_media_id');
                $fieldConfig = $resource['field_config'][$name] ?? [];
                $configuredType = (string) ($fieldConfig['type'] ?? '');
                $options = is_array($fieldConfig['options'] ?? null) ? $fieldConfig['options'] : [];
                $placeholder = (string) ($fieldConfig['placeholder'] ?? '');
                $maxLength = isset($fieldConfig['maxlength']) ? (int) $fieldConfig['maxlength'] : 500;
                $contentHint = (string) ($fieldConfig['hint'] ?? '');
                $isRichText = $configuredType === 'richtext';
                ?>
                <label class="<?= ($isText || $configuredType === 'textarea' || $isRichText) ? 'admin-field admin-field--wide' : 'admin-field' ?>">
                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($contentHint !== ''): ?><small class="admin-field__hint"><?= htmlspecialchars($contentHint, ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>

                    <?php if ($configuredType === 'select' && $options !== []): ?>
                        <select name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                            <?php foreach ($options as $optionValue => $optionLabel): ?>
                                <option value="<?= htmlspecialchars((string) $optionValue, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>><?= htmlspecialchars((string) $optionLabel, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($isMedia): ?>
                        <select name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                            <option value="">No image selected</option>
                            <?php foreach (($mediaOptions ?? []) as $media): ?>
                                <option value="<?= (int) $media['id'] ?>" <?= (string) $value === (string) $media['id'] ? 'selected' : '' ?>>
                                    #<?= (int) $media['id'] ?> — <?= htmlspecialchars($media['filename'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <a class="admin-field__media-link" href="/admin/media" target="_blank" rel="noopener">Open Media Library</a>

                    <?php elseif ($isBoolean): ?>
                        <span class="admin-toggle">
                            <input type="hidden" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="0">
                            <input type="checkbox" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="1" <?= (int) $value === 1 ? 'checked' : '' ?>>
                            <strong>Enabled</strong>
                        </span>

                    <?php elseif ($isRichText): ?>
                        <div class="rich-editor" data-rich-editor>
                            <div class="rich-editor__toolbar" role="toolbar" aria-label="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> formatting tools">
                                <button type="button" data-command="bold" title="Bold"><strong>B</strong></button>
                                <button type="button" data-command="italic" title="Italic"><em>I</em></button>
                                <button type="button" data-command="underline" title="Underline"><u>U</u></button>
                                <span class="rich-editor__divider" aria-hidden="true"></span>
                                <button type="button" data-block="p" title="Paragraph">P</button>
                                <button type="button" data-block="h2" title="Heading">H2</button>
                                <button type="button" data-block="h3" title="Subheading">H3</button>
                                <span class="rich-editor__divider" aria-hidden="true"></span>
                                <button type="button" data-command="insertUnorderedList" title="Bullet list">• List</button>
                                <button type="button" data-command="insertOrderedList" title="Numbered list">1. List</button>
                                <button type="button" data-command="formatBlock" data-value="blockquote" title="Quote">Quote</button>
                                <span class="rich-editor__divider" aria-hidden="true"></span>
                                <button type="button" data-command="createLink" title="Add link">Link</button>
                                <button type="button" data-command="unlink" title="Remove link">Unlink</button>
                                <button type="button" data-command="removeFormat" title="Clear inline formatting">Clear</button>
                            </div>
                            <div class="rich-editor__canvas" contenteditable="true" role="textbox" aria-multiline="true" data-rich-canvas></div>
                            <textarea class="rich-editor__source" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" data-rich-source hidden><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <small class="admin-field__hint">Use the toolbar for headings, bold, italics, lists, quotes and links. Your formatting is saved and shown on the public page.</small>

                    <?php elseif ($isText || $configuredType === 'textarea'): ?>
                        <textarea class="admin-rich-input" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" rows="8" maxlength="<?= $maxLength > 0 ? $maxLength : 10000 ?>" <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if ($isText): ?><small class="admin-field__hint">Separate paragraphs with a blank line. Line breaks are preserved on the public page.</small><?php endif; ?>

                    <?php elseif ($isDateTime): ?>
                        <input type="datetime-local" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '', ENT_QUOTES, 'UTF-8') ?>">

                    <?php elseif (preg_match('/int|decimal|float|double/', $type) === 1): ?>
                        <input type="number" step="any" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>">

                    <?php else: ?>
                        <input type="<?= in_array($configuredType, ['email', 'tel', 'url', 'text'], true) ? htmlspecialchars($configuredType, ENT_QUOTES, 'UTF-8') : 'text' ?>" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>" maxlength="<?= $maxLength > 0 ? $maxLength : 500 ?>" <?= $placeholder !== '' ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="admin-editor__actions">
            <a href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>">Cancel</a>
            <button type="submit"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
        </div>
    </form>
</section>

<script>
document.querySelectorAll('[data-rich-editor]').forEach(function (editor) {
    const canvas = editor.querySelector('[data-rich-canvas]');
    const source = editor.querySelector('[data-rich-source]');

    if (!canvas || !source) return;

    canvas.innerHTML = source.value || '';

    const sync = function () {
        source.value = canvas.innerHTML.trim();
    };

    canvas.addEventListener('input', sync);
    canvas.addEventListener('blur', sync);

    editor.querySelectorAll('button').forEach(function (button) {
        button.addEventListener('mousedown', function (event) {
            event.preventDefault();
            canvas.focus();

            if (button.dataset.block) {
                document.execCommand('formatBlock', false, button.dataset.block.toUpperCase());
            } else if (button.dataset.command === 'createLink') {
                const url = window.prompt('Enter the link URL');
                if (url) document.execCommand('createLink', false, url.trim());
            } else {
                document.execCommand(button.dataset.command, false, button.dataset.value || null);
            }

            sync();
        });
    });

    const form = editor.closest('form');
    if (form) form.addEventListener('submit', sync);
});
</script>
