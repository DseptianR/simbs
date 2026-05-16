/* ============================================================
   SIMBS — app.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    /* ---- NAVBAR SCROLL EFFECT ---- */
    const navbar = document.getElementById('navbar');
    const onScroll = () => {
        navbar?.classList.toggle('scrolled', window.scrollY > 50);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---- HAMBURGER MENU ---- */
    const hamburger  = document.getElementById('hamburger');
    const navMenu    = document.getElementById('navMenu');

    hamburger?.addEventListener('click', () => {
        navMenu?.classList.toggle('open');
        const isOpen = navMenu?.classList.contains('open');
        hamburger.setAttribute('aria-expanded', isOpen);
    });

    // Close menu when a link is clicked
    navMenu?.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => navMenu.classList.remove('open'));
    });

    /* ---- SEARCH OVERLAY ---- */
    const searchToggle  = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose   = document.getElementById('searchClose');

    searchToggle?.addEventListener('click', () => {
        searchOverlay?.classList.add('active');
        searchOverlay?.querySelector('.search-input')?.focus();
    });

    searchClose?.addEventListener('click', () => {
        searchOverlay?.classList.remove('active');
    });

    searchOverlay?.addEventListener('click', (e) => {
        if (e.target === searchOverlay) searchOverlay.classList.remove('active');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') searchOverlay?.classList.remove('active');
    });

    /* ---- CHAT WIDGET — dihandle sepenuhnya oleh partials/chat-widget.blade.php ---- */

    /* ---- INTERSECTION OBSERVER (SCROLL ANIMATIONS) ---- */
    const animObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.dataset.delay || 0);
                setTimeout(() => entry.target.classList.add('is-visible'), delay);
                animObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('[data-animate]').forEach(el => animObserver.observe(el));

    /* ---- COUNTER ANIMATION ---- */
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el     = entry.target;
            const target = parseInt(el.dataset.count);
            const duration = 2200;
            const startTime = performance.now();

            const tick = (now) => {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // ease out cubic
                const ease = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(ease * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString('id-ID');
            };

            requestAnimationFrame(tick);
            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-number[data-count]').forEach(el => {
        counterObserver.observe(el);
    });

    /* ---- SMOOTH SCROLL FOR ANCHOR LINKS ---- */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (!target) return;
            e.preventDefault();
            const offset = 80;
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });

});
