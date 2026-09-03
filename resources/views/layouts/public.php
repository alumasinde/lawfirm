<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$identity = $siteContent['site_identity'] ?? [];
$topBar = $siteContent['top_bar'] ?? [];
$footerExplore = $siteContent['footer_explore'] ?? [];
$footerConnect = $siteContent['footer_connect'] ?? [];
$mainNav = $navigation['main'] ?? [];
$exploreLinks = $navigation['footer_explore'] ?? [];
$connectLinks = $navigation['footer_connect'] ?? [];
$firmName = $identity['title'] ?? 'Webi Wenani';
$firmTagline = $identity['eyebrow'] ?? '';
$firmDescription = $identity['body'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#102A43">
<meta name="description" content="<?= htmlspecialchars($metaDescription ?? $firmDescription, ENT_QUOTES, 'UTF-8') ?>">
<title><?= htmlspecialchars($title ?? $firmName, ENT_QUOTES, 'UTF-8') ?></title>
<?= $styleLinker ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header">
    <?php if (!empty($topBar['title']) || (!empty($topBar['primary_label']) && !empty($topBar['primary_url']))): ?>
        <div class="site-header__top">
            <div class="site-header__top-inner">
                <?php if (!empty($topBar['title'])): ?><span><?= htmlspecialchars($topBar['title'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                <?php if (!empty($topBar['primary_label']) && !empty($topBar['primary_url'])): ?><a href="<?= htmlspecialchars($topBar['primary_url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($topBar['primary_label'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="site-header__main">
        <div class="site-header__inner">
            <a class="site-brand" href="/" aria-label="<?= htmlspecialchars($firmName, ENT_QUOTES, 'UTF-8') ?> home">
                <?= htmlspecialchars($firmName, ENT_QUOTES, 'UTF-8') ?>
                <?php if ($firmTagline !== ''): ?><span><?= htmlspecialchars($firmTagline, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            </a>
            <button class="site-nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle><span class="site-nav-toggle__icon" aria-hidden="true">☰</span></button>
            <nav class="site-nav" aria-label="Main navigation" data-site-nav>
                <?php foreach ($mainNav as $item): ?>
                    <?php $href = $item['url']; $active = $href === '/' ? $currentPath === '/' : str_starts_with($currentPath, $href); ?>
                    <a class="<?= $active ? 'is-active' : '' ?>" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
                <?php if (!empty($topBar['primary_label']) && !empty($topBar['primary_url'])): ?>
                    <a class="button button--primary site-nav__cta" href="<?= htmlspecialchars($topBar['primary_url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($topBar['primary_label'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">→</span></a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
<main id="main-content"><?= $content ?></main>
<footer class="site-footer">
    <div class="container site-footer__main">
        <div class="site-footer__grid">
            <div>
                <a class="site-brand site-footer__brand" href="/"><?= htmlspecialchars($firmName, ENT_QUOTES, 'UTF-8') ?><?php if ($firmTagline !== ''): ?><span><?= htmlspecialchars($firmTagline, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></a>
                <?php if ($firmDescription !== ''): ?><p><?= htmlspecialchars($firmDescription, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            </div>
            <div>
                <h3><?= htmlspecialchars($footerExplore['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="site-footer__links"><?php foreach ($exploreLinks as $item): ?><a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?></div>
            </div>
            <div>
                <h3><?= htmlspecialchars($footerConnect['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="site-footer__links"><?php foreach ($connectLinks as $item): ?><a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?></div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom"><div class="container"><p>&copy; <?= date('Y') ?> <?= htmlspecialchars($firmName, ENT_QUOTES, 'UTF-8') ?><?= $firmTagline !== '' ? ' ' . htmlspecialchars($firmTagline, ENT_QUOTES, 'UTF-8') : '' ?>. All rights reserved.</p></div></div>
</footer>
<script src="/js/v1/homepage-carousel.js" defer></script>
</body>
</html>
