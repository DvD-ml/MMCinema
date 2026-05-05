/**
 * Sistema de Alertas Personalizado para Admin
 * Reemplaza las alertas de Bootstrap con un sistema integrado
 */

class AdminAlerts {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Crear contenedor de alertas si no existe
        if (!document.getElementById('admin-alerts-container')) {
            this.container = document.createElement('div');
            this.container.id = 'admin-alerts-container';
            this.container.className = 'admin-alerts-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('admin-alerts-container');
        }

        // Convertir alertas Bootstrap existentes a alertas personalizadas
        this.convertBootstrapAlerts();
    }

    /**
     * Mostrar alerta flotante
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {string} title - Título de la alerta
     * @param {string} message - Mensaje de la alerta (opcional)
     * @param {number} duration - Duración en ms (0 = no se cierra automáticamente)
     */
    show(type = 'info', title = '', message = '', duration = 4000) {
        const alert = document.createElement('div');
        alert.className = `admin-alert ${type}`;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        alert.innerHTML = `
            <div class="admin-alert-icon">${icons[type] || '•'}</div>
            <div class="admin-alert-content">
                ${title ? `<div class="admin-alert-title">${this.escapeHtml(title)}</div>` : ''}
                ${message ? `<div class="admin-alert-message">${this.escapeHtml(message)}</div>` : ''}
            </div>
            <button class="admin-alert-close" aria-label="Cerrar alerta">×</button>
        `;

        // Evento para cerrar
        alert.querySelector('.admin-alert-close').addEventListener('click', () => {
            this.remove(alert);
        });

        this.container.appendChild(alert);

        // Auto-cerrar después de duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(alert);
            }, duration);
        }

        return alert;
    }

    /**
     * Mostrar alerta de éxito
     */
    success(title = 'Éxito', message = '', duration = 4000) {
        return this.show('success', title, message, duration);
    }

    /**
     * Mostrar alerta de error
     */
    error(title = 'Error', message = '', duration = 5000) {
        return this.show('error', title, message, duration);
    }

    /**
     * Mostrar alerta de advertencia
     */
    warning(title = 'Advertencia', message = '', duration = 4000) {
        return this.show('warning', title, message, duration);
    }

    /**
     * Mostrar alerta de información
     */
    info(title = 'Información', message = '', duration = 4000) {
        return this.show('info', title, message, duration);
    }

    /**
     * Remover alerta con animación
     */
    remove(alertElement) {
        alertElement.classList.add('removing');
        setTimeout(() => {
            alertElement.remove();
        }, 300);
    }

    /**
     * Convertir alertas Bootstrap existentes a alertas personalizadas
     */
    convertBootstrapAlerts() {
        // Buscar alertas Bootstrap
        const bootstrapAlerts = document.querySelectorAll('.alert');

        bootstrapAlerts.forEach(alert => {
            let type = 'info';
            let title = '';
            let message = alert.textContent.trim();

            // Determinar tipo
            if (alert.classList.contains('alert-success')) {
                type = 'success';
                title = 'Éxito';
            } else if (alert.classList.contains('alert-danger')) {
                type = 'error';
                title = 'Error';
            } else if (alert.classList.contains('alert-warning')) {
                type = 'warning';
                title = 'Advertencia';
            } else if (alert.classList.contains('alert-info')) {
                type = 'info';
                title = 'Información';
            }

            // Crear alerta personalizada
            const newAlert = document.createElement('div');
            newAlert.className = `admin-alert-inline ${type}`;

            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            newAlert.innerHTML = `
                <div class="admin-alert-icon">${icons[type]}</div>
                <div class="admin-alert-content">
                    <div class="admin-alert-title">${title}</div>
                    <div class="admin-alert-message">${message}</div>
                </div>
            `;

            // Reemplazar alerta Bootstrap
            alert.replaceWith(newAlert);
        });
    }

    /**
     * Escapar HTML para evitar XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Limpiar todas las alertas
     */
    clearAll() {
        const alerts = this.container.querySelectorAll('.admin-alert');
        alerts.forEach(alert => this.remove(alert));
    }
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.adminAlerts = new AdminAlerts();
    });
} else {
    window.adminAlerts = new AdminAlerts();
}

// Exportar para uso global
window.AdminAlerts = AdminAlerts;
