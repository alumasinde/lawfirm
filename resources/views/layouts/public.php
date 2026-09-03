<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$navItems = [
    '/' => 'Home',
    '/about' => 'About',
    '/practice-areas' => 'Practice Areas',
    '/advocates' => 'Advocates',
    '/insights' => 'Insights',
    '/faq' => 'FAQ',
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#102A43">
    <title><?= htmlspecialchars($title ?? 'Webi Wenani & Associates Advocates', ENT_QUOTES, 'UTF-8') ?></title>
    <?= $styleLinker ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<header class="site-header">
    <div class="site-header__top">
        <div class="site-header__top-inner">
            <span>Professional legal services and representation</span>
            <a href="/contact">Contact the Firm</a>
        </div>
    </div>
    <div class="site-header__main">
        <div class="site-header__inner">
            <a class="site-brand" href="/" aria-label="Webi Wenani & Associates Advocates home">
                Webi Wenani
                <span>& Associates Advocates</span>
            </a>

            <button class="site-nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle>☰</button>

            <nav class="site-nav" aria-label="Main navigation" data-site-nav>
                <?php foreach ($navItems as $href => $label): ?>
                    <?php $active = $href === '/' ? $currentPath === '/' : str_starts_with($currentPath, $href); ?>
                    <a class="<?= $active ? 'is-active' : '' ?>" href="<?= $href ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach; ?>
                <a class="button button--primary site-nav__cta" href="/contact">Book a Consultation</a>
            </nav>
        </div>
    </div>
</header>

<main id="main-content"><?= $content ?></main>

<footer class="site-footer">
    <div class="container site-footer__main">
        <div class="site-footer__grid">
            <div>
                <a class="site-brand" href="/" style="color:#fff">Webi Wenani<span>& Associates Advocates</span></a>
                <p>Clear legal advice, careful preparation and committed professional representation.</p>
            </div>
            <div>
                <h3>Explore</h3>
                <div class="site-footer__links">
                    <a href="/about">About the Firm</a>
                    <a href="/practice-areas">Practice Areas</a>
                    <a href="/advocates">Our Advocates</a>
                </div>
            </div>
            <div>
                <h3>Connect</h3>
                <div class="site-footer__links">
                    <a href="/insights">Insights & Updates</a>
                    <a href="/faq">Frequently Asked Questions</a>
                    <a href="/contact">Book a Consultation</a>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container"><p>&copy; <?= date('Y') ?> Webi Wenani & Associates Advocates. All rights reserved.</p></div>
    </div>
</footer>

<script src="/js/v1/homepage-carousel.js" defer></script>
</body>
</html>
