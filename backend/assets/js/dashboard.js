// public/assets/js/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('postsChart');
    if (!canvas) return;

    let labels = [];
    let values = [];

    try {
        labels = JSON.parse(canvas.dataset.labels || '[]');
        values = JSON.parse(canvas.dataset.values || '[]');
    } catch (e) {
        console.error('Données de graph invalides', e);
        return;
    }

    if (!labels.length || !values.length) {
        console.warn('Pas de données pour le graphique des articles.');
        return;
    }

    const ctx = canvas.getContext('2d');

    // Chart.js est chargé via CDN et fournit la variable globale "Chart"
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Articles publiés',
                data: values,
                borderColor: '#D40000',
                backgroundColor: 'rgba(212,0,0,0.25)',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                y: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff',
                        font: { size: 13 }
                    }
                }
            }
        }
    });
});

function initEventCategoryToggle() {
    const category = document.querySelector('#Event_category, #event_category, [name$="[category]"]');
    const title = document.querySelector('#Event_title, #event_title, [name$="[title]"]');
    const opponent = document.querySelector('#Event_opponent, #event_opponent, [name$="[opponent]"]');
    const matchLocation = document.querySelector('#Event_matchLocation, #event_matchLocation, [name$="[matchLocation]"]');
    const season = document.querySelector('#Event_season, #event_season, [name$="[season]"]');
    const journee = document.querySelector('#Event_journee, #event_journee, [name$="[journee]"]');

    if (!category || !season || !journee || !title || !opponent || !matchLocation) return;

    const findFieldWrapper = (input, propertyName) => {
        return input.closest(`[data-property-name="${propertyName}"], .form-group, .field-choice, .field-integer, .field-text, .mb-3`) || input.parentElement;
    };

    const titleWrapper = findFieldWrapper(title, 'title');
    const opponentWrapper = findFieldWrapper(opponent, 'opponent');
    const locationWrapper = findFieldWrapper(matchLocation, 'matchLocation');
    const seasonWrapper = findFieldWrapper(season, 'season');
    const journeeWrapper = findFieldWrapper(journee, 'journee');

    const update = () => {
        const isEvenement = category.value === 'evenement';
        const hideForEvenement = isEvenement ? 'none' : '';
        const hideForPhotoMatch = isEvenement ? '' : 'none';

        if (titleWrapper) titleWrapper.style.display = hideForPhotoMatch;
        if (opponentWrapper) opponentWrapper.style.display = hideForEvenement;
        if (locationWrapper) locationWrapper.style.display = hideForEvenement;
        if (seasonWrapper) seasonWrapper.style.display = hideForEvenement;
        if (journeeWrapper) journeeWrapper.style.display = hideForEvenement;

        title.disabled = false;
        opponent.disabled = isEvenement;
        matchLocation.disabled = isEvenement;

        season.disabled = isEvenement;
        journee.disabled = isEvenement;

        if (isEvenement) {
            opponent.value = '';
            matchLocation.value = '';
            season.value = '';
            journee.value = '';
        }
    };

    category.addEventListener('change', update);
    update();
}

function initTicketLocationToggle() {
    const matchLocation = document.querySelector('#Ticket_matchLocation, #ticket_matchLocation, [name$="[matchLocation]"]');
    const billetwebUrl = document.querySelector('#Ticket_billetwebUrl, #ticket_billetwebUrl, [name$="[billetwebUrl]"]');
    const price = document.querySelector('#Ticket_price, #ticket_price, [name$="[price]"]');
    const stock = document.querySelector('#Ticket_stock, #ticket_stock, [name$="[stock]"]');

    if (!matchLocation || !billetwebUrl || !price || !stock) return;

    const findFieldWrapper = (input, propertyName) => {
        return input.closest(`[data-property-name="${propertyName}"], .form-group, .field-text, .field-number, .field-integer, .field-choice, .mb-3`) || input.parentElement;
    };

    const billetwebWrapper = findFieldWrapper(billetwebUrl, 'billetwebUrl');
    const priceWrapper = findFieldWrapper(price, 'price');
    const stockWrapper = findFieldWrapper(stock, 'stock');

    const update = () => {
        const isHome = (matchLocation.value || '') === 'domicile' || matchLocation.value === '';

        if (billetwebWrapper) billetwebWrapper.style.display = isHome ? '' : 'none';
        if (priceWrapper) priceWrapper.style.display = isHome ? 'none' : '';
        if (stockWrapper) stockWrapper.style.display = isHome ? 'none' : '';

        if (isHome) {
            price.value = '0';
            stock.value = '0';
        } else {
            billetwebUrl.value = '';
        }
    };

    matchLocation.addEventListener('change', update);
    update();
}

document.addEventListener('DOMContentLoaded', initEventCategoryToggle);
document.addEventListener('turbo:load', initEventCategoryToggle);
document.addEventListener('DOMContentLoaded', initTicketLocationToggle);
document.addEventListener('turbo:load', initTicketLocationToggle);

function initAdminMenuHover() {
    const isMobileMenu = () => window.matchMedia('(max-width: 86rem), (hover: none)').matches;
    const sidebar = document.querySelector('body.ea .sidebar');
    const mainMenu = document.querySelector('body.ea #main-menu');
    const closeOtherItems = (currentItem) => {
        document.querySelectorAll('body.ea #main-menu .has-submenu.expanded').forEach((openItem) => {
            if (openItem !== currentItem) {
                openItem.classList.remove('expanded');
            }
        });
    };

    if (sidebar && mainMenu && !document.getElementById('adminNavToggle')) {
        const toggle = document.createElement('button');
        toggle.id = 'adminNavToggle';
        toggle.className = 'admin-burger';
        toggle.type = 'button';
        toggle.setAttribute('aria-controls', 'main-menu');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Ouvrir le menu');
        toggle.innerHTML = '<span></span><span></span><span></span>';
        sidebar.insertBefore(toggle, mainMenu);

        toggle.addEventListener('click', () => {
            const isOpen = document.body.classList.toggle('admin-mobile-nav-open');
            toggle.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    if (document.body.dataset.adminMobileSubmenusReady !== '1') {
        document.body.dataset.adminMobileSubmenusReady = '1';

        const toggleMobileSubmenu = (event) => {
            const toggle = event.target.closest('body.ea #main-menu .has-submenu > .submenu-toggle');
            if (!toggle || !isMobileMenu()) return;

            const item = toggle.closest('.has-submenu');
            if (!item) return;

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            const willOpen = !item.classList.contains('expanded');
            closeOtherItems(item);
            item.classList.toggle('expanded', willOpen);
        };

        document.addEventListener('click', toggleMobileSubmenu, true);
    }

    document.querySelectorAll('body.ea #main-menu .has-submenu').forEach((item) => {
        if (item.dataset.hoverReady === '1') return;
        item.dataset.hoverReady = '1';

        item.addEventListener('mouseenter', () => {
            if (!isMobileMenu()) {
                closeOtherItems(item);
                item.classList.add('expanded');
            }
        });

        item.addEventListener('mouseleave', () => {
            if (!isMobileMenu()) {
                item.classList.remove('expanded');
            }
        });

        item.querySelector('.submenu-toggle')?.addEventListener('click', (event) => {
            if (!isMobileMenu()) event.preventDefault();
        });
    });

    document.addEventListener('click', (event) => {
        if (!isMobileMenu() || event.target.closest('body.ea #main-menu .has-submenu')) return;

        document.querySelectorAll('body.ea #main-menu .has-submenu.expanded').forEach((item) => {
            item.classList.remove('expanded');
        });
    });

    window.matchMedia('(min-width: 86.01rem)').addEventListener('change', (event) => {
        if (!event.matches) return;

        document.body.classList.remove('admin-mobile-nav-open');
        document.getElementById('adminNavToggle')?.classList.remove('is-open');
        document.getElementById('adminNavToggle')?.setAttribute('aria-expanded', 'false');
    });
}

document.addEventListener('DOMContentLoaded', initAdminMenuHover);
document.addEventListener('turbo:load', initAdminMenuHover);
