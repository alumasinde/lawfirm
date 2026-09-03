<article class="page-section">
    <div class="container article-detail">
        <p class="section-label"><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?></p>
        <h1><?= htmlspecialchars($item['name'] ?? $item['title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($item['excerpt'])): ?><p class="lead"><?= htmlspecialchars($item['excerpt'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <?php if ($type === 'Insight' && !empty($item['cover_path'])): ?>
            <figure class="article-detail__media">
                <img src="<?= htmlspecialchars($item['cover_path'], ENT_QUOTES, 'UTF-8') ?>" alt="">
            </figure>
        <?php endif; ?>
        <?php if ($type === 'Insight' && (!empty($item['category']) || !empty($item['author_name']) || !empty($item['published_at']))): ?>
            <p class="article-detail__meta">
                <?php
                $meta = array_filter([
                    $item['category'] ?? null,
                    $item['author_name'] ?? null,
                    !empty($item['published_at']) ? date('d M Y', strtotime((string) $item['published_at'])) : null,
                ]);
                echo htmlspecialchars(implode(' · ', $meta), ENT_QUOTES, 'UTF-8');
                ?>
            </p>
        <?php endif; ?>
        <div class="rich-content"><?= nl2br(htmlspecialchars($item['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
    </div>
</article>
