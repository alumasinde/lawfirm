document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-site-nav]');

    navToggle?.addEventListener('click', () => {
        const open = nav?.classList.toggle('is-open') ?? false;
        navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        navToggle.textContent = open ? '×' : '☰';
    });

    nav?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            nav.classList.remove('is-open');
            navToggle?.setAttribute('aria-expanded', 'false');
            if (navToggle) navToggle.textContent = '☰';
        });
    });

    const carousel = document.querySelector('[data-carousel]');
    if (!carousel) return;

    const slides = [...carousel.querySelectorAll('[data-slide]')];
    const triggers = [...carousel.querySelectorAll('[data-slide-trigger]')];
    const previous = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');
    if (slides.length < 2) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 0;
    let timer;

    const show = (index) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, position) => {
            const active = position === current;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        triggers.forEach((trigger, position) => {
            trigger.classList.toggle('is-active', position === current);
            trigger.setAttribute('aria-current', position === current ? 'true' : 'false');
        });
    };

    const stop = () => window.clearInterval(timer);
    const restart = () => {
        stop();
        if (!reducedMotion) timer = window.setInterval(() => show(current + 1), 7000);
    };

    previous?.addEventListener('click', () => { show(current - 1); restart(); });
    next?.addEventListener('click', () => { show(current + 1); restart(); });
    triggers.forEach((trigger, index) => trigger.addEventListener('click', () => { show(index); restart(); }));
    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', restart);
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', restart);
    restart();
});
