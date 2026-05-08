const mmPendingArrival = (() => {
    if (window.location.pathname.includes('/admin/')) {
        return null;
    }

    const type = window.sessionStorage.getItem('mm:navigating');
    const arrivalClassByType = {
        simple: 'mm-arriving-simple'
    };

    if (!arrivalClassByType[type]) {
        return null;
    }

    const className = arrivalClassByType[type];
    document.body.classList.add(className);
    return { type, className };
})();

document.addEventListener('DOMContentLoaded', function () {
    if (document.body.classList.contains('admin-body') || window.location.pathname.includes('/admin/')) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.className = 'mm-page-transition';
    overlay.setAttribute('aria-hidden', 'true');
    document.body.appendChild(overlay);

    const transitionClasses = [
        'is-simple'
    ];
    const arrivalClasses = [
        'mm-arriving-simple'
    ];

    const resetTransition = () => {
        overlay.classList.remove(...transitionClasses);
        document.body.classList.remove(...arrivalClasses);
    };

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            resetTransition();
        }
    });
    if (mmPendingArrival) {
        window.sessionStorage.removeItem('mm:navigating');
        window.setTimeout(function () {
            document.body.classList.remove(mmPendingArrival.className);
        }, 520);
    }

    const isInternalNavigation = (link) => {
        if (!link || link.target === '_blank' || link.hasAttribute('download')) {
            return false;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return false;
        }

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) {
            return false;
        }

        if (url.pathname.includes('/admin/') || url.pathname.endsWith('/pages/logout.php')) {
            return false;
        }

        return url.href !== window.location.href || url.hash === '';
    };

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest('a');
        if (!isInternalNavigation(link)) {
            return;
        }

        event.preventDefault();
        const transitionType = 'simple';
        const delay = 180;
        const navbar = document.querySelector('.navbar');
        const top = navbar ? Math.round(navbar.getBoundingClientRect().bottom) : 0;

        overlay.classList.remove(...transitionClasses);
        overlay.style.setProperty('--mm-transition-top', `${top}px`);
        overlay.classList.add('is-simple');
        window.sessionStorage.setItem('mm:navigating', transitionType);

        window.setTimeout(function () {
            window.location.href = link.href;
        }, delay);
    });
});
