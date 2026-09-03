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
                ?>
                <label class="<?= $isText ? 'admin-field admin-field--wide' : 'admin-field' ?>">
                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if ($isMedia): ?>
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
                        <input type="hidden" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="0">
                        <input type="checkbox" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="1" <?= (int) $value === 1 ? 'checked' : '' ?>>
                    <?php elseif ($isText): ?>
                        <textarea name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" rows="8"><?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php elseif ($isDateTime): ?>
                        <input type="datetime-local" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars($value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php elseif (preg_match('/int|decimal|float|double/', $type) === 1): ?>
                        <input type="number" step="any" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>">
                    <?php else: ?>
                        <input type="text" name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>" maxlength="500">
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
