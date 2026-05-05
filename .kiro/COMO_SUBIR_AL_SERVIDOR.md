# 📤 CÓMO SUBIR LOS CAMBIOS AL SERVIDOR

## ✅ CAMBIOS LISTOS PARA SUBIR

Todos los cambios están en el commit `db8bb1c` en GitHub.

---

## 🚀 OPCIÓN 1: SUBIR VÍA GIT (RECOMENDADO)

### En el servidor:
```bash
cd /var/www/html/mmcinema
git pull origin main
```

**Ventajas:**
- ✅ Más rápido
- ✅ Mantiene el historial
- ✅ Fácil de revertir si hay problemas
- ✅ Automático

**Tiempo:** ~30 segundos

---

## 🚀 OPCIÓN 2: SUBIR VÍA FTP/SFTP

### Archivos a subir (en orden):

#### 1. CRÍTICOS (Subir primero)
```
admin/admin_header.php
admin/pages/dashboard/index.php
admin/pages/dashboard/carrusel_destacado.php
```

#### 2. Series (Estructura anidada)
```
admin/pages/series/list.php
admin/pages/series/form.php
admin/pages/series/panel.php
admin/pages/series/delete.php
admin/pages/series/save.php
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

#### 3. Otros módulos
```
admin/pages/peliculas/list.php
admin/pages/peliculas/form.php
admin/pages/peliculas/delete.php
admin/pages/peliculas/save.php

admin/pages/noticias/list.php
admin/pages/noticias/form.php
admin/pages/noticias/delete.php
admin/pages/noticias/save.php

admin/pages/proyecciones/list.php
admin/pages/proyecciones/form.php
admin/pages/proyecciones/delete.php
admin/pages/proyecciones/save.php
admin/pages/proyecciones/api.php

admin/pages/salas/list.php
admin/pages/salas/form.php
admin/pages/salas/delete.php
admin/pages/salas/save.php

admin/pages/usuarios/list.php
admin/pages/usuarios/form.php
admin/pages/usuarios/delete.php
admin/pages/usuarios/save.php

admin/pages/criticas/list.php
admin/pages/criticas/form.php
admin/pages/criticas/delete.php
admin/pages/criticas/save.php
```

#### 4. Helpers
```
admin/helpers/series_admin_ui.php
admin/helpers/upload_helper.php
```

#### 5. Assets
```
assets/css/admin-alerts.css
assets/js/admin-alerts.js
```

**Ventajas:**
- ✅ Control total
- ✅ Puedes subir solo lo que necesitas

**Tiempo:** ~5-10 minutos

---

## 🚀 OPCIÓN 3: SUBIR VÍA SCRIPT

### Crear un script en el servidor:

```bash
#!/bin/bash
cd /var/www/html/mmcinema
git fetch origin
git checkout origin/main -- admin/pages/ admin/helpers/ admin/admin_header.php assets/css/admin-alerts.css assets/js/admin-alerts.js
echo "✅ Cambios subidos correctamente"
```

---

## ✅ VERIFICACIÓN DESPUÉS DE SUBIR

### 1. Verificar que los archivos están en el servidor
```bash
ls -la /var/www/html/mmcinema/admin/pages/dashboard/
ls -la /var/www/html/mmcinema/admin/pages/series/
```

### 2. Verificar permisos
```bash
chmod -R 755 /var/www/html/mmcinema/admin/pages/
chmod -R 755 /var/www/html/mmcinema/admin/helpers/
chmod 644 /var/www/html/mmcinema/admin/admin_header.php
```

### 3. Probar en el navegador

#### Dashboard
```
http://tu-servidor/admin/pages/dashboard/index.php
```
✅ Debe cargar sin errores
✅ Todos los botones deben funcionar

#### Películas
```
http://tu-servidor/admin/pages/peliculas/list.php
```
✅ Debe cargar sin errores "Not Found"
✅ Las imágenes deben verse

#### Series
```
http://tu-servidor/admin/pages/series/list.php
```
✅ Debe cargar sin errores
✅ Los posters deben verse

#### Temporadas
```
http://tu-servidor/admin/pages/series/temporadas/list.php
```
✅ Debe cargar sin errores
✅ Estructura anidada debe funcionar

#### Episodios
```
http://tu-servidor/admin/pages/series/episodios/list.php
```
✅ Debe cargar sin errores
✅ Estructura anidada debe funcionar

#### Carrusel
```
http://tu-servidor/admin/pages/dashboard/carrusel_destacado.php
```
✅ Debe cargar sin errores
✅ Las imágenes deben verse

---

## 🔍 SOLUCIÓN DE PROBLEMAS

### Si ves "Not Found"
1. Verifica que los archivos están en la ruta correcta
2. Verifica los permisos (755 para carpetas, 644 para archivos)
3. Verifica que el servidor tiene PHP habilitado
4. Revisa los logs del servidor: `/var/log/apache2/error.log`

### Si ves errores de conexión a BD
1. Verifica que la BD está corriendo
2. Verifica las credenciales en `config/conexion.php`
3. Verifica que el usuario de BD tiene permisos

### Si ves errores de rutas
1. Verifica que las rutas absolutas comienzan con `/`
2. Verifica que los archivos existen en `/assets/`
3. Verifica que los helpers existen en `/admin/helpers/`

---

## 📋 CHECKLIST FINAL

- [ ] Archivos subidos al servidor
- [ ] Permisos configurados correctamente
- [ ] Dashboard carga sin errores
- [ ] Películas carga sin errores
- [ ] Series carga sin errores
- [ ] Temporadas carga sin errores
- [ ] Episodios carga sin errores
- [ ] Carrusel carga sin errores
- [ ] Todas las imágenes se ven
- [ ] Todos los enlaces funcionan
- [ ] Puedo crear elementos
- [ ] Puedo editar elementos
- [ ] Puedo eliminar elementos

---

## 🎯 RESUMEN

**Cambios:** 18 problemas críticos corregidos
**Archivos:** 188 modificados
**Commit:** db8bb1c
**Estado:** ✅ Listo para producción

**Tiempo estimado de subida:** 30 segundos (Git) o 5-10 minutos (FTP)

---

**¿Necesitas ayuda? Revisa los logs del servidor o contacta al equipo de desarrollo.**
