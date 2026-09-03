<section class="page-hero page-hero--compact">
    <div class="container">
        <p class="section-label">Practice Area</p>
        <h1><?= htmlspecialchars($area['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($area['excerpt'])): ?><p class="lead"><?= htmlspecialchars($area['excerpt'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>

<section class="page-section practice-detail">
    <div class="container practice-detail__grid">
        <div class="practice-detail__main">
            <section class="practice-detail__section">
                <p class="section-label">Overview</p>
                <div class="rich-content"><?= nl2br(htmlspecialchars((string) ($area['body'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></div>
            </section>

            <?php if ($details['experience'] !== []): ?>
                <section class="practice-detail__section">
                    <p class="section-label">Experience</p>
                    <h2>Experience in this area</h2>
                    <p class="practice-detail__intro">Some of our recent experience in this area includes:</p>
                    <ol class="practice-experience">
                        <?php foreach ($details['experience'] as $item): ?><li><?= nl2br(htmlspecialchars((string) $item['content'], ENT_QUOTES, 'UTF-8')) ?></li><?php endforeach; ?>
                    </ol>
                </section>
            <?php endif; ?>

            <?php if ($details['insights'] !== []): ?>
                <section class="practice-detail__section">
                    <p class="section-label">Recent Insights</p>
                    <h2>Articles and publications</h2>
                    <div class="practice-insights">
                        <?php foreach ($details['insights'] as $article): ?>
                            <a href="/insights/<?= rawurlencode($article['slug']) ?>">
                                <?php if (!empty($article['category'])): ?><span><?= htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                                <strong><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small>Read insight →</small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <aside class="practice-detail__sidebar">
            <?php if ($details['contacts'] !== []): ?>
                <section class="practice-contacts">
                    <p class="section-label">Key Contacts</p>
                    <?php foreach ($details['contacts'] as $contact): ?>
                        <?php $name = trim((string) $contact['first_name'] . ' ' . (string) $contact['last_name']); ?>
                        <article>
                            <h2><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h2>
                            <?php if (!empty($contact['title'])): ?><p><?= htmlspecialchars($contact['title'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                            <?php if (!empty($contact['email'])): ?><a href="mailto:<?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?php if ($details['related'] !== []): ?>
                <section class="practice-related">
                    <p class="section-label">Related Services</p>
                    <div>
                        <?php foreach ($details['related'] as $related): ?><a href="/practice-areas/<?= rawurlencode($related['slug']) ?>"><?= htmlspecialchars($related['name'], ENT_QUOTES, 'UTF-8') ?><span>→</span></a><?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </aside>
    </div>
</section>
