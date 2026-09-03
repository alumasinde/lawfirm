<?php
$isPracticeAreas = ($resource['resource_key'] ?? '') === 'practice-areas';
?>
<section class="admin-page">
    <div class="admin-page__intro">
        <div>
            <p class="admin-kicker"><?= $isPracticeAreas ? 'Practice area management' : 'Content management' ?></p>
            <h1><?= htmlspecialchars($resource['label'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p><?= htmlspecialchars((string) ($resource['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a class="admin-primary-action" href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>/create">Add new</a>
    </div>

    <?php if ($isPracticeAreas): ?>
        <div class="practice-management-guide">
            <div class="practice-management-guide__icon">01</div>
            <div>
                <strong>Edit page content</strong>
                <span>Name, URL, hero excerpt, overview, approach, call to action and SEO.</span>
            </div>
            <div class="practice-management-guide__icon">02</div>
            <div>
                <strong>Manage page details</strong>
                <span>Key advocates, representative experience, related insights and related services.</span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="admin-notice" role="status">Changes were saved successfully.</div>
    <?php endif; ?>

    <section class="admin-panel">
        <form class="admin-search" method="get">
            <input type="search" name="q" value="<?= htmlspecialchars($listing['search'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Search <?= htmlspecialchars($resource['label'], ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Search</button>
            <?php if ($listing['search'] !== ''): ?>
                <a href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>">Clear</a>
            <?php endif; ?>
        </form>

        <?php if ($listing['rows'] === []): ?>
            <div class="admin-empty-state">
                <h2>No records found</h2>
                <p>Create the first record or adjust your search.</p>
            </div>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table>
                    <thead>
                    <tr>
                        <?php foreach ($resource['list_columns'] as $column): ?>
                            <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $column)), ENT_QUOTES, 'UTF-8') ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($listing['rows'] as $row): ?>
                        <tr>
                            <?php foreach ($resource['list_columns'] as $column): ?>
                                <?php $value = $row[$column] ?? ''; ?>
                                <td><?= htmlspecialchars(is_scalar($value) ? (string) $value : json_encode($value), ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                            <td class="admin-row-actions<?= $isPracticeAreas ? ' admin-row-actions--practice' : '' ?>">
                                <?php if ($isPracticeAreas): ?>
                                    <div class="practice-row-actions">
                                        <a class="practice-row-action practice-row-action--primary" href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>/<?= (int) $row['id'] ?>/edit">
                                            <strong>Edit content</strong>
                                            <small>Text, excerpt, SEO & page sections</small>
                                        </a>
                                        <a class="practice-row-action" href="/admin/practice-areas/<?= (int) $row['id'] ?>/details">
                                            <strong>Manage details</strong>
                                            <small>Contacts, experience & related content</small>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <a href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>/<?= (int) $row['id'] ?>/edit">Edit</a>
                                <?php endif; ?>
                                <form method="post" action="/admin/content/<?= rawurlencode($resource['resource_key']) ?>/<?= (int) $row['id'] ?>/delete" onsubmit="return confirm('Delete this record?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars(AppCoreCsrf::token(), ENT_QUOTES, 'UTF-8') ?>">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($listing['pages'] > 1): ?>
                <nav class="admin-pagination" aria-label="Pagination">
                    <?php for ($page = 1; $page <= $listing['pages']; $page++): ?>
                        <a class="<?= $page === $listing['page'] ? ' is-active' : '' ?>" href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>?page=<?= $page ?>&q=<?= rawurlencode($listing['search']) ?>"><?= $page ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</section>