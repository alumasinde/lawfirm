<?php
$title = $title ?? 'Administrator';
$user = $user ?? null;
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
        <span><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span>
        <form method="post" action="/admin/logout">
            <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Logout</button>
        </form>
    </div>
</header>
<?php endif; ?>
<main class="<?= $user === null ? 'admin-auth' : 'admin-main' ?>"><?= $content ?></main>
</body>
</html>
