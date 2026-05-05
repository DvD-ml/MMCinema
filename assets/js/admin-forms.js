/**
 * Admin Forms - Mejoras de formularios
 * Validación visual, contador de caracteres, etc.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres en textareas
    const textareas = document.querySelectorAll('.admin-form-textarea');
    textareas.forEach(textarea => {
        const charCount = textarea.parentElement.querySelector('.char-count');
        if (charCount) {
            // Actualizar contador inicial
            charCount.textContent = textarea.value.length;
            
            // Actualizar al escribir
            textarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    });

    // Validación visual en tiempo real
    const inputs = document.querySelectorAll('.admin-form-input, .admin-form-textarea, .admin-form-select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    // Preview de imágenes
    const fileInputs = document.querySelectorAll('.admin-form-file');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar tamaño
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    showAlert('error', 'El archivo es demasiado grande. Máximo 5MB.');
                    this.value = '';
                    return;
                }

                // Validar tipo
                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showAlert('error', 'Formato no válido. Usa JPG, PNG o WebP.');
                    this.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.style.maxWidth = '180px';
                    preview.style.borderRadius = '10px';
                    preview.style.marginTop = '12px';
                    preview.style.border = '1px solid rgba(249,115,22,.3)';
                    
                    const existingPreview = input.parentElement.querySelector('img[style*="maxWidth"]');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    
                    input.parentElement.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

/**
 * Validar campo individual
 */
function validateField(field) {
    if (!field.value.trim() && field.hasAttribute('required')) {
        field.classList.add('is-invalid');
        return false;
    }
    
    field.classList.remove('is-invalid');
    return true;
}

/**
 * Mostrar alerta
 */
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

/**
 * Validar formulario antes de enviar
 */
function validateForm(form) {
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}
