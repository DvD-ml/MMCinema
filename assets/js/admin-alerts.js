/**
 * Sistema de alertas del panel admin.
 */
class AdminAlerts {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        this.container = document.getElementById('admin-alerts-container');

        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'admin-alerts-container';
            this.container.className = 'admin-alerts-container';
            document.body.appendChild(this.container);
        }

        this.convertBootstrapAlerts();
        this.autoDismissInlineAlerts(5000);
    }

    show(type = 'info', title = '', message = '', duration = 5000) {
        const alert = document.createElement('div');
        alert.className = `admin-alert ${type}`;

        const icons = {
            success: '\u2713',
            error: '\u2715',
            warning: '\u26a0',
            info: '\u2139'
        };

        alert.innerHTML = `
            <div class="admin-alert-icon">${icons[type] || '\u2022'}</div>
            <div class="admin-alert-content">
                ${title ? `<div class="admin-alert-title">${this.escapeHtml(title)}</div>` : ''}
                ${message ? `<div class="admin-alert-message">${this.escapeHtml(message)}</div>` : ''}
            </div>
            <button class="admin-alert-close" aria-label="Cerrar alerta">&times;</button>
        `;

        alert.querySelector('.admin-alert-close').addEventListener('click', () => {
            this.remove(alert);
        });

        this.container.appendChild(alert);

        if (duration > 0) {
            setTimeout(() => this.remove(alert), duration);
        }

        return alert;
    }

    success(title = 'Exito', message = '', duration = 5000) {
        return this.show('success', title, message, duration);
    }

    error(title = 'Error', message = '', duration = 5000) {
        return this.show('error', title, message, duration);
    }

    warning(title = 'Advertencia', message = '', duration = 5000) {
        return this.show('warning', title, message, duration);
    }

    info(title = 'Informacion', message = '', duration = 5000) {
        return this.show('info', title, message, duration);
    }

    remove(alertElement) {
        if (!alertElement || alertElement.classList.contains('removing')) {
            return;
        }

        alertElement.classList.add('removing');
        setTimeout(() => {
            alertElement.remove();
        }, 350);
    }

    autoDismissInlineAlerts(duration = 5000) {
        const inlineAlerts = document.querySelectorAll('.admin-alert-inline');

        inlineAlerts.forEach(alert => {
            window.setTimeout(() => {
                this.remove(alert);
            }, duration);
        });
    }

    convertBootstrapAlerts() {
        const bootstrapAlerts = document.querySelectorAll('.alert');

        bootstrapAlerts.forEach(alert => {
            let type = 'info';
            let title = 'Informacion';
            const message = alert.textContent.trim();

            if (alert.classList.contains('alert-success')) {
                type = 'success';
                title = 'Exito';
            } else if (alert.classList.contains('alert-danger')) {
                type = 'error';
                title = 'Error';
            } else if (alert.classList.contains('alert-warning')) {
                type = 'warning';
                title = 'Advertencia';
            }

            const icons = {
                success: '\u2713',
                error: '\u2715',
                warning: '\u26a0',
                info: '\u2139'
            };

            const newAlert = document.createElement('div');
            newAlert.className = `admin-alert-inline ${type}`;
            newAlert.innerHTML = `
                <div class="admin-alert-icon">${icons[type]}</div>
                <div class="admin-alert-content">
                    <div class="admin-alert-title">${title}</div>
                    <div class="admin-alert-message">${this.escapeHtml(message)}</div>
                </div>
            `;

            alert.replaceWith(newAlert);
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    clearAll() {
        const alerts = document.querySelectorAll('.admin-alert, .admin-alert-inline');
        alerts.forEach(alert => this.remove(alert));
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.adminAlerts = new AdminAlerts();
    });
} else {
    window.adminAlerts = new AdminAlerts();
}

window.AdminAlerts = AdminAlerts;
