let adminConfirmState = {
    form: null,
    modal: null,
    confirmButton: null,
    messageNode: null,
    titleNode: null,
};

function getBootstrapModal(element) {
    if (!element || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        return null;
    }

    return bootstrap.Modal.getOrCreateInstance(element, {
        backdrop: 'static',
        keyboard: true,
    });
}

function setButtonLoading(button, loading) {
    if (!(button instanceof HTMLButtonElement)) {
        return;
    }

    button.disabled = loading;
    button.classList.toggle('is-loading', loading);
    button.dataset.loading = loading ? '1' : '0';
}

function submitConfirmedForm(form) {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    form.dataset.adminConfirmed = '1';

    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        if (button instanceof HTMLButtonElement || button instanceof HTMLInputElement) {
            button.disabled = true;
        }
    });

    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
        return;
    }

    form.submit();
}

function initAdminConfirmations() {
    const modalElement = document.getElementById('adminConfirmModal');
    const titleNode = modalElement ? modalElement.querySelector('[data-admin-confirm-title]') : null;
    const messageNode = modalElement ? modalElement.querySelector('[data-admin-confirm-message]') : null;
    const confirmButton = modalElement ? modalElement.querySelector('[data-admin-confirm-action]') : null;
    const modal = getBootstrapModal(modalElement);

    adminConfirmState = {
        form: null,
        modal,
        confirmButton,
        messageNode,
        titleNode,
    };

    document.querySelectorAll('[data-admin-confirm]').forEach((form) => {
        if (!(form instanceof HTMLFormElement) || form.dataset.confirmReady === '1') {
            return;
        }

        form.dataset.confirmReady = '1';
        form.addEventListener('submit', (event) => {
            if (form.dataset.adminConfirmed === '1') {
                return;
            }

            event.preventDefault();

            const message = form.getAttribute('data-admin-confirm') || 'Confirmer cette action ?';
            const title = form.getAttribute('data-admin-confirm-title') || 'Confirmer l\'action ?';

            if (!adminConfirmState.modal || !adminConfirmState.confirmButton || !adminConfirmState.messageNode) {
                if (window.confirm(message)) {
                    submitConfirmedForm(form);
                }
                return;
            }

            adminConfirmState.form = form;
            adminConfirmState.messageNode.textContent = message;
            if (adminConfirmState.titleNode) {
                adminConfirmState.titleNode.textContent = title;
            }

            adminConfirmState.confirmButton.disabled = false;
            adminConfirmState.confirmButton.textContent = 'Confirmer';
            adminConfirmState.modal.show();
        });
    });

    if (adminConfirmState.confirmButton) {
        adminConfirmState.confirmButton.addEventListener('click', () => {
            if (!adminConfirmState.form) {
                return;
            }

            setButtonLoading(adminConfirmState.confirmButton, true);
            adminConfirmState.confirmButton.textContent = 'En cours...';
            submitConfirmedForm(adminConfirmState.form);
        });
    }
}

function initAdminSidebarToggle() {
    const sidebar = document.querySelector('.admin-sidebar');
    const backdrop = document.querySelector('[data-admin-sidebar-close]');
    const toggle = document.querySelector('[data-admin-sidebar-toggle]');

    if (!sidebar || !backdrop || !toggle) {
        return;
    }

    const openSidebar = () => {
        sidebar.classList.add('is-open');
        backdrop.hidden = false;
        backdrop.classList.add('is-visible');
        document.body.classList.add('admin-sidebar-open');
    };

    const closeSidebar = () => {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-visible');
        document.body.classList.remove('admin-sidebar-open');
        window.setTimeout(() => {
            if (!sidebar.classList.contains('is-open')) {
                backdrop.hidden = true;
            }
        }, 180);
    };

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('is-open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    backdrop.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminConfirmations();
    initAdminSidebarToggle();
});

document.addEventListener('turbo:load', () => {
    initAdminConfirmations();
    initAdminSidebarToggle();
});
