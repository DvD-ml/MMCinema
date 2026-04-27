# 🎬 MMCinema - Plataforma de Gestión de Cine

Plataforma web completa para gestión de cine digital con sistema de reservas, críticas y administración de contenido.

## 📋 Requisitos Previos

- **PHP 7.4 o superior**
- **MySQL 5.7 o superior** / MariaDB
- **Composer** (gestor de dependencias PHP)
- **Servidor web** (Apache/Nginx) o XAMPP/WAMP/MAMP
- **Cuenta de Gmail** (para envío de correos)

---

## 🚀 Instalación

### 1. Clonar o descargar el proyecto

```bash
cd C:\xampp\htdocs
# o la carpeta de tu servidor web
```

### 2. Instalar dependencias

```bash
cd MMCinema
composer install
```

### 3. Configurar variables de entorno

Copia el archivo de ejemplo y edítalo con tus credenciales:

```bash
copy .env.example .env
```

Edita el archivo `.env` con tus datos:

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_NAME=mmcinema3
DB_USER=root
DB_PASS=tu_contraseña

# Configuración de Correo (Gmail SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_FROM_EMAIL=tu_email@gmail.com
MAIL_FROM_NAME=MMCinema

# URL Base del Proyecto
BASE_URL=http://localhost/MMCinema

# Entorno (development, production)
APP_ENV=development
```

⚠️ **Importante:** Para Gmail, debes usar una **contraseña de aplicación**, no tu contraseña normal.

**Cómo obtener contraseña de aplicación de Gmail:**
1. Ve a tu cuenta de Google → Seguridad
2. Activa la verificación en 2 pasos
3. Ve a "Contraseñas de aplicaciones"
4. Genera una nueva contraseña para "Correo"
5. Copia esa contraseña en `MAIL_PASSWORD`

### 4. Crear la base de datos

Abre phpMyAdmin o tu cliente MySQL y ejecuta:

```sql
CREATE DATABASE mmcinema3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Importar el esquema de base de datos

**Opción A: Usando phpMyAdmin (Recomendado)**

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `mmcinema3`
3. Ve a la pestaña "Importar"
4. Selecciona el archivo `sql/mmcinema3.sql`
5. Haz clic en "Continuar"

**Opción B: Línea de comandos**

```bash
# Windows (PowerShell)
& "C:\xampp\mysql\bin\mysql.exe" -u root -p mmcinema3 < sql/mmcinema3.sql

# Linux/Mac
mysql -u root -p mmcinema3 < sql/mmcinema3.sql
```

**📋 Credenciales por defecto después de importar:**
- Email: `admin@mmcinema.com`
- Contraseña: `admin123`

⚠️ **Cambia esta contraseña inmediatamente**

📖 **Más detalles:** Ver `INSTRUCCIONES_BD.md`

### 6. Configurar permisos de carpetas

Las siguientes carpetas necesitan permisos de escritura:

```bash
# En Windows (PowerShell como administrador)
icacls logs /grant Everyone:F
icacls cache /grant Everyone:F
icacls tickets /grant Everyone:F
icacls img /grant Everyone:F
icacls assets /grant Everyone:F

# En Linux/Mac
chmod -R 755 logs cache tickets img assets
```

### 7. Configurar el servidor web

#### Opción A: XAMPP/WAMP (Recomendado para desarrollo)

1. Coloca el proyecto en `C:\xampp\htdocs\MMCinema`
2. Inicia Apache y MySQL desde el panel de control
3. Accede a: `http://localhost/MMCinema`

#### Opción B: Servidor PHP integrado

```bash
cd MMCinema
php -S localhost:8000
```

Accede a: `http://localhost:8000`

---

## 🎯 Primer Uso

### 1. Acceder a la aplicación

Abre tu navegador y ve a:
```
http://localhost/MMCinema
```

### 2. Crear usuario administrador

Si no tienes usuarios en la BD, crea uno manualmente:

```sql
INSERT INTO usuario (username, email, password, rol, verificado, creado) 
VALUES (
    'admin',
    'admin@mmcinema.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'admin',
    1,
    NOW()
);
```

**Credenciales por defecto:**
- Email: `admin@mmcinema.com`
- Contraseña: `admin123`

⚠️ **Cambia esta contraseña inmediatamente después del primer login**

### 3. Acceder al panel de administración

```
http://localhost/MMCinema/admin
```

---

## 📁 Estructura del Proyecto

```
MMCinema/
├── admin/              # Panel de administración
├── backend/            # Lógica de negocio (AJAX/POST)
├── config/             # Configuración (BD, correos)
├── css/                # Estilos
├── helpers/            # Clases auxiliares (Validator, CSRF, Logger)
├── img/                # Imágenes (posters, noticias)
├── assets/             # Recursos estáticos
├── logs/               # Logs de la aplicación
├── cache/              # Caché de datos
├── tickets/            # PDFs de entradas generadas
├── vendor/             # Dependencias de Composer
├── .env                # Variables de entorno (NO SUBIR A GIT)
├── .env.example        # Plantilla de variables
├── composer.json       # Dependencias PHP
└── index.php           # Página principal
```

---

## 🔧 Configuración Adicional

### Configurar URL amigables (Opcional)

Crea un archivo `.htaccess` en la raíz:

```apache
RewriteEngine On
RewriteBase /MMCinema/

# Redirigir www a no-www
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 [R=301,L]

# Proteger archivos sensibles
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### Configurar tareas programadas (Opcional)

Para limpiar logs antiguos, tickets expirados, etc:

```bash
# Crontab (Linux/Mac)
0 2 * * * php /ruta/a/MMCinema/scripts/limpiar_logs.php

# Task Scheduler (Windows)
# Crear tarea que ejecute: php C:\xampp\htdocs\MMCinema\scripts\limpiar_logs.php
```

---

## 🧪 Verificar Instalación

### 1. Verificar conexión a BD

Accede a: `http://localhost/MMCinema/config/test_conexion.php`

Si ves "Conexión exitosa", todo está bien.

### 2. Verificar envío de correos

Intenta registrar un usuario nuevo y verifica que llegue el correo de verificación.

### 3. Verificar logs

Revisa que se estén generando logs en `logs/app.log`

---

## 🐛 Solución de Problemas

### Error: "Class 'Dotenv\Dotenv' not found"

```bash
composer install
```

### Error: "Access denied for user 'root'@'localhost'"

Verifica las credenciales en `.env`:
- `DB_USER` y `DB_PASS` correctos
- MySQL está corriendo

### Error: "Table 'mmcinema3.pelicula' doesn't exist"

Importa el esquema de la base de datos (paso 5 de instalación)

### Error: "Failed to send email"

Verifica:
- Credenciales de Gmail en `.env`
- Usas contraseña de aplicación (no contraseña normal)
- Verificación en 2 pasos activada en Gmail

### Error: "Permission denied" al escribir logs/cache

Configura permisos de escritura (paso 6 de instalación)

### Las imágenes no se muestran

Verifica:
- Carpetas `img/` y `assets/` tienen permisos de lectura
- La ruta `BASE_URL` en `.env` es correcta

---

## 📚 Documentación Adicional

- [Mejoras Implementadas](MEJORAS_IMPLEMENTADAS.md) - Changelog de mejoras de seguridad
- [Guía de Desarrollo](docs/DESARROLLO.md) - Para desarrolladores
- [API Documentation](docs/API.md) - Endpoints disponibles

---

## 🔐 Seguridad

- ✅ Credenciales en variables de entorno
- ✅ Protección CSRF en formularios
- ✅ Validación centralizada de inputs
- ✅ Logging de actividad
- ✅ Prepared statements en todas las queries
- ⚠️ **NUNCA** subir `.env` a Git
- ⚠️ Cambiar contraseñas por defecto en producción

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📝 Licencia

Este proyecto es privado y de uso educativo.

---

## 👨‍💻 Autor

David Monzón López

---

## 📞 Soporte

Si tienes problemas con la instalación:
1. Revisa la sección "Solución de Problemas"
2. Verifica los logs en `logs/app.log`
3. Contacta al desarrollador
