# 🎨 Guía de Uso - Sistema de Alertas Personalizado

## Descripción General

Se ha implementado un nuevo sistema de alertas personalizado que reemplaza las alertas de Bootstrap estándar. Las alertas ahora tienen un diseño moderno, integrado con el tema oscuro/naranja del admin y con animaciones suaves.

---

## 📍 Tipos de Alertas

### 1. Alertas Flotantes (Toast)
Aparecen en la esquina superior derecha y se cierran automáticamente.

**Uso en JavaScript:**
```javascript
// Éxito (verde)
adminAlerts.success('Proyección guardada', 'Los cambios se han guardado correctamente');

// Error (rojo)
adminAlerts.error('Error', 'No se pudo guardar la proyección');

// Advertencia (naranja)
adminAlerts.warning('Advertencia', 'Esta acción no se puede deshacer');

// Información (azul)
adminAlerts.info('Información', 'Se han cargado 5 proyecciones');
```

**Parámetros:**
- `title` (string): Título de la alerta
- `message` (string): Mensaje descriptivo
- `duration` (number): Milisegundos antes de cerrar (0 = no se cierra automáticamente)

---

### 2. Alertas Inline
Se muestran en la página, debajo del encabezado.

**Uso en PHP:**
```php
<?php if (isset($_GET['ok'])): ?>
    <div class="admin-alert-inline success">
        <div class="admin-alert-icon">✓</div>
        <div class="admin-alert-content">
            <div class="admin-alert-title">Éxito</div>
            <div class="admin-alert-message">La proyección se ha guardado correctamente.</div>
        </div>
    </div>
<?php endif; ?>
```

---

## 🎯 Casos de Uso

### Panel de Proyecciones

**Cuando se guarda una proyección:**
```javascript
adminAlerts.success('Proyección actualizada', 'Los cambios se han guardado correctamente');
```

**Cuando hay error:**
```javascript
adminAlerts.error('Error', 'No se pudo guardar la proyección');
```

**Cuando falta un campo:**
```javascript
adminAlerts.warning('Validación', 'Por favor completa todos los campos');
```

### Otras Páginas Admin

**Películas:**
- ✓ Película guardada
- ✕ Error al guardar película
- ✕ Error en la imagen

**Usuarios:**
- ✓ Usuario guardado
- ✕ Email ya existe
- ✕ Usuario ya existe

**Salas:**
- ✓ Sala guardada
- ✕ Error al procesar sala

---

## 🎨 Colores y Estilos

| Tipo | Color | Icono | Uso |
|------|-------|-------|-----|
| Success | Verde (#10b981) | ✓ | Operaciones exitosas |
| Error | Rojo (#ef4444) | ✕ | Errores y fallos |
| Warning | Naranja (#f97316) | ⚠ | Advertencias |
| Info | Azul (#3b82f6) | ℹ | Información general |

---

## ⚙️ Configuración

### Duración de Alertas Flotantes

```javascript
// Cierre automático en 4 segundos (por defecto)
adminAlerts.success('Título', 'Mensaje');

// Cierre automático en 5 segundos
adminAlerts.error('Título', 'Mensaje', 5000);

// No se cierra automáticamente
adminAlerts.info('Título', 'Mensaje', 0);
```

### Limpiar Todas las Alertas

```javascript
adminAlerts.clearAll();
```

---

## 📱 Responsive

Las alertas se adaptan automáticamente:
- **Desktop:** Esquina superior derecha, ancho máximo 400px
- **Tablet:** Ancho máximo 400px
- **Móvil:** Ancho completo (con márgenes)

---

## 🔄 Conversión Automática

Las alertas Bootstrap existentes se convierten automáticamente:

```html
<!-- Antes (Bootstrap) -->
<div class="alert alert-success">Guardado correctamente</div>

<!-- Después (Automático) -->
<div class="admin-alert-inline success">
    <div class="admin-alert-icon">✓</div>
    <div class="admin-alert-content">
        <div class="admin-alert-title">Éxito</div>
        <div class="admin-alert-message">Guardado correctamente</div>
    </div>
</div>
```

---

## 🚀 Ejemplos Prácticos

### Ejemplo 1: Guardar Proyección

```javascript
function guardarProyeccion() {
    const form = document.getElementById('proyeccionEditForm');
    
    // Validar
    if (!form.fecha.value) {
        adminAlerts.warning('Validación', 'Por favor selecciona una fecha');
        return;
    }
    
    // Enviar
    fetch('proyeccion_guardar.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(response => {
        if (response.ok) {
            adminAlerts.success('Proyección actualizada', 'Los cambios se han guardado correctamente');
            cerrarPanelEdicion();
        } else {
            adminAlerts.error('Error', 'No se pudo guardar la proyección');
        }
    })
    .catch(error => {
        adminAlerts.error('Error', 'Ocurrió un error: ' + error);
    });
}
```

### Ejemplo 2: Eliminar Proyección

```javascript
function eliminarProyeccion(id) {
    if (confirm('¿Estás seguro?')) {
        fetch('proyeccion_borrar.php', {
            method: 'POST',
            body: new FormData(document.getElementById('deleteForm'))
        })
        .then(response => {
            if (response.ok) {
                adminAlerts.success('Proyección eliminada', 'La proyección se ha eliminado correctamente');
                recargarLista();
            }
        });
    }
}
```

---

## 🔧 Personalización

### Cambiar Duración Global

Edita `assets/js/admin-alerts.js`:
```javascript
// Línea ~150
if (duration > 0) {
    setTimeout(() => {
        this.remove(alert);
    }, duration); // Cambiar aquí
}
```

### Cambiar Colores

Edita `assets/css/admin-alerts.css`:
```css
/* Línea ~80 - Success */
.admin-alert.success {
    border-left-color: #10b981; /* Cambiar color aquí */
    background: rgba(5, 46, 22, 0.6);
}
```

---

## ✅ Checklist de Implementación

- [x] Crear estilos CSS para alertas
- [x] Crear lógica JavaScript para alertas
- [x] Integrar en admin_header.php
- [x] Actualizar proyecciones.php
- [x] Actualizar peliculas.php
- [x] Actualizar usuarios.php
- [x] Actualizar criticas.php
- [x] Actualizar salas.php
- [x] Actualizar sala_form.php
- [x] Actualizar carrusel_destacado.php
- [x] Corregir API de proyecciones
- [x] Agregar panel de edición de proyecciones
- [x] Probar en navegador

---

## 🐛 Troubleshooting

### Las alertas no aparecen
1. Verifica que `admin-alerts.css` esté incluido en el header
2. Verifica que `admin-alerts.js` esté incluido en el header
3. Abre la consola del navegador (F12) y busca errores

### Las alertas aparecen pero sin estilos
1. Limpia el caché del navegador (Ctrl+Shift+Delete)
2. Verifica que los archivos CSS estén en `assets/css/`
3. Verifica que los archivos JS estén en `assets/js/`

### Las alertas no se cierran automáticamente
1. Verifica que `duration` sea mayor a 0
2. Verifica que no haya errores en la consola

---

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

---

**Última actualización:** 4 de Mayo de 2026
**Versión:** 1.0
