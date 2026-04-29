# 🚀 Guía Rápida de Inicio - MMCinema

## 📋 Requisitos Previos

- ✅ XAMPP instalado (Apache + MySQL + PHP 7.4+)
- ✅ Navegador web moderno (Chrome, Firefox, Edge)
- ✅ Acceso a phpMyAdmin

---

## ⚡ Inicio Rápido (5 minutos)

### 1. Iniciar Servicios XAMPP

```bash
# Abrir XAMPP Control Panel
# Iniciar Apache
# Iniciar MySQL
```

### 2. Verificar Base de Datos

1. Abrir phpMyAdmin: `http://localhost/phpmyadmin`
2. Verificar que existe la base de datos `mmcinema`
3. Si no existe, importar: `database/mmcinema.sql`

### 3. Verificar Configuración

Archivo: `.env`
```env
DB_HOST=localhost
DB_NAME=mmcinema
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=tu_email@gmail.com
MAIL_PASS=tu_contraseña_app
MAIL_FROM=tu_email@gmail.com
MAIL_NAME=MMCinema
```

### 4. Abrir la Aplicación

```
http://localhost/david/MMCINEMA/
```

---

## 🔑 Credenciales de Prueba

### Usuario Admin
- **Email:** admin@mmcinema.com
- **Contraseña:** admin123

### Usuario Normal
- **Email:** usuario@mmcinema.com
- **Contraseña:** usuario123

*(Nota: Estos usuarios deben existir en la BD)*

---

## 📍 URLs Principales

### Páginas Públicas
- **Inicio:** `http://localhost/david/MMCINEMA/`
- **Cartelera:** `http://localhost/david/MMCINEMA/cartelera.php`
- **Series:** `http://localhost/david/MMCINEMA/series.php`
- **Noticias:** `http://localhost/david/MMCINEMA/noticias.php`
- **Login:** `http://localhost/david/MMCINEMA/login.php`
- **Registro:** `http://localhost/david/MMCINEMA/registro.php`

### Panel Admin
- **Dashboard:** `http://localhost/david/MMCINEMA/admin/`
- **Películas:** `http://localhost/david/MMCINEMA/admin/peliculas.php`
- **Proyecciones:** `http://localhost/david/MMCINEMA/admin/proyecciones.php`
- **Salas:** `http://localhost/david/MMCINEMA/admin/salas.php`
- **Series:** `http://localhost/david/MMCINEMA/admin/series_panel.php`
- **Noticias:** `http://localhost/david/MMCINEMA/admin/noticias.php`
- **Usuarios:** `http://localhost/david/MMCINEMA/admin/usuarios.php`

---

## 🎯 Primeros Pasos

### Como Usuario

1. **Registrarse**
   - Ir a Registro
   - Rellenar formulario
   - Verificar email (revisar spam)
   - Iniciar sesión

2. **Explorar Cartelera**
   - Ver películas disponibles
   - Filtrar por género, fecha, sala
   - Ver detalles de película

3. **Reservar Entradas**
   - Seleccionar película
   - Elegir proyección
   - Seleccionar asientos
   - Confirmar reserva
   - Descargar ticket

4. **Explorar Series**
   - Ver catálogo de series
   - Ver detalles de serie
   - Ver temporadas y episodios
   - Agregar a favoritos

5. **Escribir Críticas**
   - Ir a detalle de película/serie
   - Escribir crítica
   - Dar puntuación (1-5 estrellas)
   - Enviar

### Como Admin

1. **Acceder al Panel**
   - Iniciar sesión como admin
   - Click en "Panel Admin" en navbar
   - Ver dashboard

2. **Gestionar Películas**
   - Ir a Películas
   - Agregar nueva película
   - Subir poster
   - Guardar

3. **Gestionar Proyecciones**
   - Ir a Proyecciones
   - Agregar nueva proyección
   - Seleccionar película y sala
   - Definir fecha y hora
   - Guardar

4. **Gestionar Salas**
   - Ir a Salas
   - Agregar nueva sala
   - Definir capacidad
   - Guardar

5. **Gestionar Series**
   - Ir a Series
   - Agregar nueva serie
   - Agregar temporadas
   - Agregar episodios
   - Subir imágenes

6. **Gestionar Carrusel**
   - Ir a Carrusel Destacado
   - Agregar elemento
   - Subir imágenes (fondo y logo)
   - Definir orden
   - Activar

---

## 🛠️ Solución de Problemas Comunes

### Problema: "No se puede conectar a la base de datos"

**Solución:**
1. Verificar que MySQL está iniciado en XAMPP
2. Verificar credenciales en `.env`
3. Verificar que la base de datos existe
4. Probar conexión: `http://localhost/david/MMCINEMA/config/test_conexion.php`

### Problema: "CSS no carga"

**Solución:**
1. Verificar que Apache está iniciado
2. Limpiar caché del navegador (Ctrl + F5)
3. Verificar ruta en el código fuente
4. Verificar permisos de carpeta `assets/`

### Problema: "Imágenes no cargan"

**Solución:**
1. Verificar que las imágenes existen en `assets/img/`
2. Verificar permisos de carpeta
3. Verificar rutas en el código
4. Limpiar caché del navegador

### Problema: "Token CSRF inválido"

**Solución:**
1. Limpiar cookies del navegador
2. Cerrar sesión y volver a iniciar
3. Verificar que el formulario tiene el campo CSRF
4. Verificar que la sesión está activa

### Problema: "No puedo subir imágenes"

**Solución:**
1. Verificar permisos de carpetas de upload
2. Verificar tamaño máximo en `php.ini`:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`
3. Reiniciar Apache después de cambiar `php.ini`

### Problema: "Smooth scroll no funciona"

**Solución:**
1. Verificar que Lenis está incluido
2. Abrir consola del navegador (F12)
3. Buscar errores de JavaScript
4. Verificar que `lenis-scripts.php` está incluido

### Problema: "Correos no se envían"

**Solución:**
1. Verificar configuración SMTP en `.env`
2. Usar contraseña de aplicación de Gmail
3. Activar "Acceso de apps menos seguras" en Gmail
4. Verificar logs en `logs/app.log`

---

## 📁 Estructura de Carpetas Importante

```
/david/MMCINEMA/
├── pages/           # Todas las páginas públicas
├── admin/           # Panel de administración
├── backend/         # Procesamiento de formularios
├── assets/          # CSS, imágenes, JS
│   ├── css/         # Hojas de estilo
│   ├── img/         # Imágenes
│   │   ├── posters/     # Posters de películas
│   │   ├── carrusel/    # Imágenes del carrusel
│   │   ├── logos/       # Logos de contenido
│   │   ├── noticias/    # Imágenes de noticias
│   │   └── series/      # Imágenes de series
│   └── js/          # JavaScript
├── components/      # Componentes reutilizables
├── config/          # Configuración
├── helpers/         # Clases auxiliares
├── logs/            # Logs del sistema
└── storage/         # Almacenamiento (tickets)
```

---

## 🔐 Seguridad

### Protección CSRF
- Todos los formularios tienen token CSRF
- Los tokens se validan en el backend
- Los tokens se regeneran después del login

### Rate Limiting
- Máximo 5 intentos de login en 15 minutos
- Bloqueo temporal después de 5 intentos
- Logs de seguridad en `logs/security.log`

### Validaciones
- Validación de email
- Validación de contraseña (mínimo 6 caracteres)
- Validación de campos obligatorios
- Protección contra SQL injection
- Protección contra XSS

### Archivos Sensibles
- `.env` bloqueado por `.htaccess`
- `.sql` bloqueado por `.htaccess`
- `.log` bloqueado por `.htaccess`
- Carpetas sensibles bloqueadas

---

## 📊 Logs del Sistema

### Ubicación
```
logs/
├── app.log          # Log general de la aplicación
├── security.log     # Log de eventos de seguridad
└── error.log        # Log de errores
```

### Ver Logs
```bash
# Ver últimas 50 líneas del log de aplicación
tail -n 50 logs/app.log

# Ver últimas 50 líneas del log de seguridad
tail -n 50 logs/security.log

# Ver últimas 50 líneas del log de errores
tail -n 50 logs/error.log
```

---

## 🎨 Personalización

### Cambiar Logo
1. Reemplazar `assets/img/logo.png`
2. Reemplazar `assets/img/logo2.png`
3. Limpiar caché del navegador

### Cambiar Colores
1. Editar `assets/css/base.css`
2. Buscar variables CSS:
   ```css
   :root {
     --color-primary: #e50914;
     --color-secondary: #221f1f;
     --color-accent: #f59e0b;
   }
   ```
3. Cambiar valores
4. Guardar y recargar

### Cambiar Favicon
1. Reemplazar `favicon-16x16.png`
2. Reemplazar `favicon-32x32.png`
3. Reemplazar `apple-touch-icon.png`
4. Limpiar caché del navegador

---

## 📚 Documentación Adicional

- **Estructura del Proyecto:** `docs/ESTRUCTURA_PROYECTO.md`
- **Reorganización Completada:** `docs/REORGANIZACION_COMPLETADA.md`
- **Corrección de Enlaces:** `docs/CORRECCION_ENLACES_NAVEGACION.md`
- **Resumen Final:** `docs/RESUMEN_FINAL_REORGANIZACION.md`
- **Checklist de Verificación:** `docs/CHECKLIST_VERIFICACION_FINAL.md`

---

## 🆘 Soporte

### Problemas Técnicos
1. Revisar logs en `logs/`
2. Revisar consola del navegador (F12)
3. Revisar documentación en `docs/`
4. Buscar en el código fuente

### Contacto
- **Email:** soporte@mmcinema.com
- **GitHub:** github.com/mmcinema/mmcinema
- **Documentación:** docs/

---

## ✅ Checklist de Inicio

- [ ] XAMPP instalado y funcionando
- [ ] Apache iniciado
- [ ] MySQL iniciado
- [ ] Base de datos importada
- [ ] Archivo `.env` configurado
- [ ] Aplicación accesible en navegador
- [ ] Login funciona
- [ ] Panel admin accesible
- [ ] CSS e imágenes cargan correctamente
- [ ] Smooth scroll funciona

---

## 🎉 ¡Listo!

Si has completado todos los pasos, tu instalación de MMCinema está **lista para usar**.

Disfruta explorando todas las funcionalidades del proyecto.

---

**Última actualización:** 29 de abril de 2026  
**Versión:** 2.0  
**Estado:** ✅ Producción Ready
