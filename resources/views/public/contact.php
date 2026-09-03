<?php $page = $page ?? null; ?>
<section class="page-hero">
    <div class="container">
        <?php if (!empty($page['eyebrow'])): ?><p class="section-label"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <h1><?= htmlspecialchars($page['title'] ?? 'Contact the Firm', ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($page['body'])): ?><p class="lead"><?= htmlspecialchars($page['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    </div>
</section>
<section class="page-section">
    <div class="container contact-layout">
        <div class="form-wrap">
            <?php if ($status === 'success'): ?><div class="notice notice--success">Thank you. Your enquiry has been received.</div><?php elseif ($status === 'invalid'): ?><div class="notice">Please check your details and try again.</div><?php endif; ?>
            <form method="post" action="/contact" class="contact-form">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <label>Name<input name="name" required maxlength="180" autocomplete="name"></label>
                <label>Email<input type="email" name="email" maxlength="190" autocomplete="email"></label>
                <label>Phone<input name="phone" maxlength="50" autocomplete="tel"></label>
                <label>Subject<input name="subject" maxlength="255"></label>
                <label>Message<textarea name="message" required rows="7"></textarea></label>
                <button class="button button--primary" type="submit">Send Enquiry <span aria-hidden="true">→</span></button>
            </form>
        </div>
        <aside class="contact-panel">
            <?php if (!empty($page['eyebrow'])): ?><p class="section-label section-label--light"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <h2><?= htmlspecialchars($details['title'] ?? $page['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if (!empty($details['body'])): ?><p><?= htmlspecialchars($details['body'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <div class="contact-panel__item"><strong>Response</strong><span>Provide your preferred contact details in your enquiry.</span></div>
            <div class="contact-panel__item"><strong>Legal Services</strong><span>Explore the firm's practice areas before sending your enquiry.</span></div>
            <div class="contact-panel__item"><strong>Privacy</strong><span>Please avoid sending highly sensitive documents through the initial contact form.</span></div>
        </aside>
    </div>
</section>
