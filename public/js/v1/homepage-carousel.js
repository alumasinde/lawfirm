document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.querySelector('[data-carousel]');

    if (!carousel) return;

    const slides = [...carousel.querySelectorAll('[data-slide]')];
    const triggers = [...carousel.querySelectorAll('[data-slide-trigger]')];
    const previous = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');

    if (slides.length < 2) return;

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
        });
    };

    const restart = () => {
        window.clearInterval(timer);
        timer = window.setInterval(() => show(current + 1), 7000);
    };

    previous?.addEventListener('click', () => { show(current - 1); restart(); });
    next?.addEventListener('click', () => { show(current + 1); restart(); });
    triggers.forEach((trigger, index) => trigger.addEventListener('click', () => { show(index); restart(); }));

    carousel.addEventListener('mouseenter', () => window.clearInterval(timer));
    carousel.addEventListener('mouseleave', restart);

    restart();
});
