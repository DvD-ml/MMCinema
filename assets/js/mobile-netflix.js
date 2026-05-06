(function () {
    const mobileQuery = window.matchMedia('(max-width: 768px)');

    function enhanceRail(row, type) {
        if (!row || row.dataset.mobileNetflixReady === '1') return;
        row.dataset.mobileNetflixReady = '1';
        row.classList.add('mm-mobile-rail');
        if (type === 'news') row.classList.add('mm-mobile-news-rail');
    }

    function setupRails() {
        if (!mobileQuery.matches) return;

        document.querySelectorAll('main .row').forEach((row) => {
            if (row.querySelector('.noticia-card')) {
                enhanceRail(row, 'news');
                return;
            }

            if (row.querySelector('.pelicula-card, .serie-card')) {
                enhanceRail(row, 'poster');
            }
        });

        document.querySelectorAll('.series-row-scroll, .series-featured-slider, .streaming-platforms').forEach(enableDragScroll);
        document.querySelectorAll('.mm-mobile-rail').forEach(enableDragScroll);
        document.querySelectorAll('.pelicula-card, .serie-card, .noticia-card, .critica-card').forEach(makeCardTappable);
        setupNavbarFallback();
    }

    function setupNavbarFallback() {
        const toggler = document.querySelector('.navbar-toggler[data-bs-target="#menu"]');
        const menu = document.getElementById('menu');

        if (!toggler || !menu || toggler.dataset.mobileFallbackReady === '1') return;

        toggler.dataset.mobileFallbackReady = '1';
        toggler.setAttribute('aria-controls', 'menu');
        toggler.setAttribute('aria-expanded', menu.classList.contains('show') ? 'true' : 'false');
        toggler.setAttribute('aria-label', 'Abrir menu');

        toggler.addEventListener('click', () => {
            if (!mobileQuery.matches) return;

            window.setTimeout(() => {
                const bootstrapReady = Boolean(window.bootstrap && window.bootstrap.Collapse);

                if (!bootstrapReady) {
                    menu.classList.toggle('show');
                }

                const expanded = menu.classList.contains('show');
                toggler.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }, 0);
        });

        menu.querySelectorAll('a[href]').forEach((link) => {
            link.addEventListener('click', () => {
                if (!mobileQuery.matches) return;
                menu.classList.remove('show');
                toggler.setAttribute('aria-expanded', 'false');
            });
        });
    }

    function makeCardTappable(card) {
        if (!card || card.dataset.mobileTapReady === '1') return;

        const link = card.querySelector('a[href]');
        if (!link) return;

        card.dataset.mobileTapReady = '1';
        card.setAttribute('role', 'link');
        card.tabIndex = 0;

        card.addEventListener('click', (event) => {
            if (!mobileQuery.matches) return;
            if (event.target.closest('a, button, input, select, textarea')) return;
            window.location.href = link.href;
        });

        card.addEventListener('keydown', (event) => {
            if (!mobileQuery.matches) return;
            if (event.key !== 'Enter' && event.key !== ' ') return;
            event.preventDefault();
            window.location.href = link.href;
        });
    }

    function enableDragScroll(element) {
        if (!element || element.dataset.dragScrollReady === '1') return;
        element.dataset.dragScrollReady = '1';

        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;
        let moved = false;

        element.addEventListener('pointerdown', (event) => {
            if (!mobileQuery.matches || event.pointerType === 'mouse') return;
            isDown = true;
            moved = false;
            startX = event.clientX;
            scrollLeft = element.scrollLeft;
            element.setPointerCapture?.(event.pointerId);
        }, { passive: true });

        element.addEventListener('pointermove', (event) => {
            if (!isDown) return;
            const distance = event.clientX - startX;
            if (Math.abs(distance) > 6) moved = true;
            element.scrollLeft = scrollLeft - distance;
        }, { passive: true });

        function finish(event) {
            if (!isDown) return;
            isDown = false;
            element.releasePointerCapture?.(event.pointerId);
        }

        element.addEventListener('pointerup', finish, { passive: true });
        element.addEventListener('pointercancel', finish, { passive: true });

        element.addEventListener('click', (event) => {
            if (!moved) return;
            event.preventDefault();
            event.stopPropagation();
            moved = false;
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupRails);
    } else {
        setupRails();
    }

    mobileQuery.addEventListener?.('change', setupRails);
})();
