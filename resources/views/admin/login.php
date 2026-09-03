<section class="admin-login-card" aria-labelledby="admin-login-title">
    <div class="admin-login-card__brand">Webi Wenani <span>& Associates Advocates</span></div>
    <p class="admin-login-card__eyebrow">Secure administration</p>
    <h1 id="admin-login-title">Sign in to the admin area</h1>
    <p>Use an administrator account to manage the website.</p>

    <?php if (!empty($error)): ?>
        <div class="admin-alert" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" action="/admin/login" class="admin-form" autocomplete="on">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <label>Email
            <input type="email" name="email" required autocomplete="username" maxlength="190">
        </label>
        <label>Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button type="submit">Sign in securely</button>
    </form>
</section>
