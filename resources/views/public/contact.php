<section class="page-hero">
    <div class="container">
        <p class="section-label">Get in Touch</p>
        <h1>Contact the Firm</h1>
        <p class="lead">Send an enquiry with a brief outline of your matter and the firm can respond using the details you provide.</p>
    </div>
</section>

<section class="page-section">
    <div class="container contact-layout">
        <div class="form-wrap">
            <?php if ($status === 'success'): ?>
                <div class="notice notice--success">Thank you. Your enquiry has been received.</div>
            <?php elseif ($status === 'invalid'): ?>
                <div class="notice">Please check your details and try again.</div>
            <?php endif; ?>

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
            <p class="section-label section-label--light">Consultation</p>
            <h2>Let us discuss how we can assist.</h2>
            <p>Use the enquiry form to make an initial contact with the firm and provide the best details for a response.</p>
            <div class="contact-panel__item">
                <strong>Response</strong>
                <span>Provide your preferred contact details in your enquiry.</span>
            </div>
            <div class="contact-panel__item">
                <strong>Legal Services</strong>
                <span>Explore the firm's practice areas before sending your enquiry.</span>
            </div>
            <div class="contact-panel__item">
                <strong>Privacy</strong>
                <span>Please avoid sending highly sensitive documents through the initial contact form.</span>
            </div>
        </aside>
    </div>
</section>
