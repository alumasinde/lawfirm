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
    <div class="site-header__inner">
        <a class="site-brand" href="/">Webi Wenani & Associates Advocates</a>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="/">Home</a>
            <a href="/about">About</a>
            <a href="/practice-areas">Practice Areas</a>
            <a href="/advocates">Advocates</a>
            <a href="/contact">Contact</a>
        </nav>
    </div>
</header>
<main id="main-content"><?= $content ?></main>
<footer class="site-footer">
    <div class="container">
        <p>Webi Wenani & Associates Advocates</p>
    </div>
</footer>
</body>
</html>
