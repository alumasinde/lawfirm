<?php
$title = $title ?? 'Administrator';
$user = $user ?? null;
$resources = $resources ?? [];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow,noarchive">
<title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin-shell">
<?php if ($user !== null): ?>
<header class="admin-topbar">
    <a href="/admin" class="admin-brand">Webi Wenani <span>Administration</span></a>
    <div class="admin-user">
        <a href="/" target="_blank" rel="noopener">View site</a>
        <span><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span>
        <form method="post" action="/admin/logout">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>
<div class="admin-workspace">
    <aside class="admin-sidebar" aria-label="Administration navigation">
        <a class="admin-nav-link" href="/admin">Overview</a>
        <a class="admin-nav-link" href="/admin/manage">Website management</a>
        <?php foreach ($resources as $resource): ?>
            <a class="admin-nav-link" href="/admin/content/<?= rawurlencode($resource['resource_key']) ?>"><?= htmlspecialchars($resource['label'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </aside>
    <main class="admin-main"><?= $content ?></main>
</div>
<?php else: ?>
<main class="admin-auth"><?= $content ?></main>
<?php endif; ?>
</body>
</html>
