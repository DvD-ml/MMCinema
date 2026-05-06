# 📤 ARCHIVOS A SUBIR AL SERVIDOR

## ✅ CAMBIOS COMPLETADOS Y LISTOS PARA SUBIR

El commit `db8bb1c` contiene todas las correcciones de rutas del panel admin. Aquí están los archivos **CRÍTICOS** que deben subirse al servidor:

---

## 🔴 ARCHIVOS CRÍTICOS (DEBEN SUBIRSE PRIMERO)

### Admin Header (Afecta a TODO el panel)
```
admin/admin_header.php
```

### Dashboard
```
admin/pages/dashboard/index.php
admin/pages/dashboard/carrusel_destacado.php
```

### Series (Estructura anidada)
```
admin/pages/series/list.php
admin/pages/series/form.php
admin/pages/series/panel.php
admin/pages/series/delete.php
admin/pages/series/criticas/list.php
admin/pages/series/temporadas/form.php
admin/pages/series/temporadas/list.php
admin/pages/series/temporadas/delete.php
admin/pages/series/temporadas/save.php
admin/pages/series/episodios/form.php
admin/pages/series/episodios/list.php
admin/pages/series/episodios/delete.php
admin/pages/series/episodios/save.php
```

### Otros módulos
```
admin/pages/peliculas/list.php
admin/pages/peliculas/form.php
admin/pages/noticias/list.php
admin/pages/noticias/form.php
admin/pages/proyecciones/list.php
admin/pages/proyecciones/form.php
admin/pages/salas/list.php
admin/pages/salas/form.php
admin/pages/usuarios/list.php
admin/pages/usuarios/form.php
admin/pages/criticas/list.php
admin/pages/criticas/form.php
```

### Helpers
```
admin/helpers/series_admin_ui.php
```

### Assets (CSS y JS)
```
assets/css/admin-alerts.css
assets/js/admin-alerts.js
```

---

## 📋 RESUMEN DE CAMBIOS

### ✅ Rutas Corregidas:
- ✅ admin_header.php: Rutas absolutas para assets
- ✅ carrusel_destacado.php: 5 rutas de directorios corregidas
- ✅ dashboard/index.php: 4 enlaces corregidos
- ✅ series/list.php: Helper path y image paths corregidos
- ✅ series/form.php: Image paths corregidos
- ✅ series/temporadas/form.php: Image paths corregidos
- ✅ series/criticas/list.php: Helper path corregido

### 🎯 Total de problemas resueltos: 18

---

## 🚀 INSTRUCCIONES DE SUBIDA

### Opción 1: Subir vía FTP/SFTP
1. Conectar al servidor con FTP/SFTP
2. Navegar a `/var/www/html/mmcinema/`
3. Subir los archivos listados arriba manteniendo la estructura de carpetas

### Opción 2: Subir vía Git (Recomendado)
```bash
# En el servidor
cd /var/www/html/mmcinema
git pull origin main
```

### Opción 3: Subir archivos específicos
Si solo quieres subir los archivos críticos, usa esta lista:

**CRÍTICOS (subir primero):**
- admin/admin_header.php
- admin/pages/dashboard/index.php
- admin/pages/dashboard/carrusel_destacado.php

**DESPUÉS:**
- Todos los archivos en admin/pages/
- admin/helpers/series_admin_ui.php
- assets/css/admin-alerts.css
- assets/js/admin-alerts.js

---

## ✅ VERIFICACIÓN DESPUÉS DE SUBIR

Después de subir los archivos, verifica:

1. **Dashboard**: http://servidor/admin/pages/dashboard/index.php
   - ✅ Debe cargar sin errores
   - ✅ Todos los botones deben funcionar

2. **Películas**: http://servidor/admin/pages/peliculas/list.php
   - ✅ Debe cargar sin errores "Not Found"
   - ✅ Las imágenes deben verse

3. **Series**: http://servidor/admin/pages/series/list.php
   - ✅ Debe cargar sin errores
   - ✅ Los posters deben verse

4. **Series → Temporadas**: http://servidor/admin/pages/series/temporadas/list.php
   - ✅ Debe cargar sin errores
   - ✅ Estructura anidada debe funcionar

5. **Series → Episodios**: http://servidor/admin/pages/series/episodios/list.php
   - ✅ Debe cargar sin errores
   - ✅ Estructura anidada debe funcionar

6. **Carrusel**: http://servidor/admin/pages/dashboard/carrusel_destacado.php
   - ✅ Debe cargar sin errores
   - ✅ Las imágenes deben verse

---

## 📝 NOTAS IMPORTANTES

- **NO** necesitas subir los archivos de documentación (.md)
- **NO** necesitas subir los archivos antiguos (fueron eliminados)
- **SÍ** necesitas subir la estructura completa de `admin/pages/`
- **SÍ** necesitas subir `admin/helpers/`
- **SÍ** necesitas subir los nuevos archivos CSS y JS

---

## 🔗 COMMIT INFORMACIÓN

- **Commit**: db8bb1c
- **Mensaje**: Fix: Corregir todas las rutas del panel admin - 18 problemas críticos resueltos
- **Archivos modificados**: 188
- **Insertions**: 18554
- **Deletions**: 6711

---

**Estado**: ✅ LISTO PARA PRODUCCIÓN
