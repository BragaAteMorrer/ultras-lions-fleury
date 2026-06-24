/* =============================
   LOADING SCREEN
============================= */
(function () {
    const hideLoader = () => {
        document.body.classList.add('is-loaded');
    };

    if (document.readyState === 'complete') {
        window.setTimeout(hideLoader, 250);
        return;
    }

    window.addEventListener('load', () => window.setTimeout(hideLoader, 250));
    window.setTimeout(hideLoader, 1800);
})();

/* =============================
   NAVBAR : Effet au scroll
============================= */
(function () {
    const header = document.querySelector('.site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        header.classList.toggle('is-scrolled', window.scrollY > 10);
    });
})();

/* =============================
   NAVBAR : Menu Mobile
============================= */
(function () {
    const navToggle = document.getElementById('navToggle');
    const header = document.querySelector('.site-header');

    if (navToggle) {
        navToggle.addEventListener('click', () => {
            header.classList.toggle('nav-open');
        });
    }
})();

/* =============================
   LIGHTBOX ULTRAS (galeries)
============================= */
(function () {
    const getItems = (groupName) => {
        if (!groupName) return [];
        return Array.from(document.querySelectorAll(`[data-lightbox="${groupName}"]`));
    };

    let currentItems = [];
    let currentIndex = -1;
    let overlay = null;
    let image = null;

    const close = () => {
        if (!overlay) return;
        overlay.remove();
        overlay = null;
        image = null;
        currentItems = [];
        currentIndex = -1;
        document.body.style.overflow = '';
    };

    const show = (index) => {
        if (!currentItems.length) return;

        currentIndex = (index + currentItems.length) % currentItems.length;
        const link = currentItems[currentIndex];
        const src = link.getAttribute('href');
        if (!src || !image) return;

        image.src = src;
        image.alt = link.querySelector('img')?.alt || 'Image';
    };

    const open = (groupName, clickedLink) => {
        currentItems = getItems(groupName);
        currentIndex = currentItems.indexOf(clickedLink);
        if (currentIndex < 0) return;

        overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.innerHTML = `
            <button type="button" class="lightbox-btn lightbox-close" aria-label="Fermer">×</button>
            <button type="button" class="lightbox-btn lightbox-prev" aria-label="Précédent">‹</button>
            <div class="lightbox-inner">
                <img src="" alt="">
            </div>
            <button type="button" class="lightbox-btn lightbox-next" aria-label="Suivant">›</button>
        `;

        image = overlay.querySelector('.lightbox-inner img');
        if (!image) return;

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) close();
        });

        overlay.querySelector('.lightbox-close')?.addEventListener('click', close);
        overlay.querySelector('.lightbox-prev')?.addEventListener('click', () => show(currentIndex - 1));
        overlay.querySelector('.lightbox-next')?.addEventListener('click', () => show(currentIndex + 1));

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        show(currentIndex);
    };

    document.addEventListener('click', (e) => {
        const target = e.target.closest('[data-lightbox]');
        if (!target) return;

        e.preventDefault();
        open(target.getAttribute('data-lightbox'), target);
    });

    document.addEventListener('keydown', (e) => {
        if (!overlay) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(currentIndex - 1);
        if (e.key === 'ArrowRight') show(currentIndex + 1);
    });
})();

// Navbar Ultra toggle
(function () {
    const btn = document.getElementById("ultraNavToggle");
    const nav = document.getElementById("ultraNav");

    if (!btn || !nav) return;

    btn.addEventListener("click", () => {
        nav.classList.toggle("open");
    });
})();

(() => {
    const btn = document.getElementById('ultraNavToggle');
    const nav = document.getElementById('ultraNavMobile');
    if (!btn || !nav) return;
    btn.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
})();

(() => {
    const toggles = document.querySelectorAll('#ultraNavMobile .submenu-toggle');
    if (!toggles.length) return;

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const parent = toggle.closest('.mobile-submenu');
            if (!parent) return;
            const isOpen = parent.classList.toggle('open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
})();
