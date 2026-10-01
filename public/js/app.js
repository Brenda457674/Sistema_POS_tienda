/**
 * POS Tienda — scripts globales
 */

document.addEventListener('DOMContentLoaded', function () {
    // ---------- Sidebar móvil ----------
    const sidebar = document.getElementById('posSidebar');
    const overlay = document.getElementById('posSidebarOverlay');
    const toggle = document.getElementById('posSidebarToggle');

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
    };

    toggle?.addEventListener('click', function () {
        sidebar?.classList.toggle('show');
        overlay?.classList.toggle('show');
    });

    overlay?.addEventListener('click', closeSidebar);

    // ---------- Confirmaciones (modal reutilizable) ----------
    const confirmModalEl = document.getElementById('posConfirmModal');
    const confirmTitle = document.getElementById('posConfirmTitle');
    const confirmMessage = document.getElementById('posConfirmMessage');
    const confirmAccept = document.getElementById('posConfirmAccept');

    if (confirmModalEl && window.bootstrap) {
        const confirmModal = new bootstrap.Modal(confirmModalEl);
        let pendingForm = null;

        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) {
                return;
            }

            if (form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();
            pendingForm = form;

            confirmTitle.textContent = form.dataset.confirmTitle || '¿Estás seguro?';
            confirmMessage.textContent =
                form.dataset.confirm || 'Esta acción no se puede deshacer.';
            confirmAccept.textContent = form.dataset.confirmText || 'Sí, continuar';

            confirmModal.show();
        });

        confirmAccept?.addEventListener('click', function () {
            if (pendingForm) {
                pendingForm.dataset.confirmed = '1';
                confirmModal.hide();
                pendingForm.submit();
            }
        });
    }

    // ---------- Auto-ocultar alertas ----------
    window.setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            try {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            } catch (e) {
                // sin bootstrap disponible
            }
        });
    }, 6000);
});
