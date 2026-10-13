document.addEventListener('DOMContentLoaded', () => {
    const adminToggle = document.getElementById('adminNavToggle');
    const adminMobileNav = document.getElementById('adminNavMobile');

    if (adminToggle && adminMobileNav) {
        adminToggle.addEventListener('click', () => {
            const isOpen = adminMobileNav.classList.toggle('open');

            adminToggle.classList.toggle('is-open', isOpen);
            adminToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.classList.toggle('mobile-nav-open', isOpen);
        });
    }

    document.querySelectorAll('.admin-ultra-header .mobile-submenu .submenu-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const submenu = button.closest('.mobile-submenu');

            if (!submenu) {
                return;
            }

            const isOpen = submenu.classList.toggle('open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
});