# 📚 DOCUMENTACIÓN DEL PROYECTO MMCINEMA

## 📋 ÍNDICE

1. [Inicio Rápido](#inicio-rápido)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Configuración](#configuración)
4. [Características Implementadas](#características-implementadas)
5. [Documentación Técnica](#documentación-técnica)

---

## 🚀 INICIO RÁPIDO

### Requisitos:
- XAMPP (Apache + MySQL + PHP 8.0+)
- Composer
- Navegador moderno

### Instalación:

1. **Clonar el proyecto** en `C:\xampp\htdocs\david\MMCINEMA`

2. **Configurar base de datos:**
   ```bash
   # Importar base de datos
   mysql -u root < database/mmcinema3.sql
   ```

3. **Configurar variables de entorno:**
   ```bash
   cp .env.example .env
   # Editar .env con tus credenciales
   ```

4. **Instalar dependencias:**
   ```bash
   composer install
   ```

5. **Acceder a la aplicación:**
   - Web: `http://localhost/david/MMCINEMA/`
   - Admin: `http://localhost/david/MMCINEMA/admin/`

---

## 📁 ESTRUCTURA DEL PROYECTO

```
MMCINEMA/
├── pages/              # Páginas principales de la aplicación
├── components/         # Componentes reutilizables (navbar, footer)
├── admin/              # Panel de administración
├── backend/            # Lógica de procesamiento
├── config/             # Configuración (BD, mail)
├── helpers/            # Clases auxiliares (CSRF, Logger, etc.)
├── assets/             # CSS, JS, imágenes
├── database/           # SQL y migraciones
├── storage/            # Archivos generados (tickets, logs)
├── tests/              # Scripts de prueba
└── docs/               # Documentación
```

---

## ⚙️ CONFIGURACIÓN

### Base de Datos:
- Ver: `docs/INSTRUCCIONES_BD.md`

### Variables de Entorno:
- Ver: `docs/NOTA_ENV_ESPACIOS.md`

### Correo Electrónico:
- Configurar en `config/mail.php`
- Requiere credenciales SMTP en `.env`

---

## ✨ CARACTERÍSTICAS IMPLEMENTADAS

### Funcionalidades Principales:
✅ Sistema de autenticación (login, registro, verificación email)
✅ Cartelera de películas con reserva de entradas
✅ Catálogo de series (streaming)
✅ Sistema de críticas y valoraciones
✅ Noticias de cine
✅ Perfil de usuario estilo Letterboxd
✅ Panel de administración completo
✅ Gestión de proyecciones y salas
✅ Generación de tickets PDF con QR

### Seguridad:
✅ Protección CSRF en todos los formularios
✅ Rate limiting en login
✅ Validación de archivos (MIME type)
✅ Logging de eventos de seguridad
✅ Sesiones seguras con "Remember Me"

### UX/UI:
✅ Diseño responsive (móvil, tablet, desktop)
✅ Smooth scroll con Lenis
✅ Carrusel destacado en home
✅ Autocompletado en formularios
✅ Estética tipo Netflix/streaming

---

## 📖 DOCUMENTACIÓN TÉCNICA

### Documentos Principales:

- **REORGANIZACION_COMPLETADA.md** - Estructura actual del proyecto
- **INICIO_RAPIDO.md** - Guía de inicio rápido
- **INSTRUCCIONES_BD.md** - Configuración de base de datos
- **RESUMEN_COMPLETO_FINAL.md** - Resumen completo de funcionalidades

### Características Específicas:

- **AUTOCOMPLETE_IMPLEMENTADO.md** - Autocompletado de formularios
- **LENIS_IMPLEMENTADO.md** - Smooth scroll
- **REMEMBER_ME_IMPLEMENTADO.md** - Sesión persistente
- **VERIFICACION_CUENTA.md** - Verificación por email
- **PERFIL_LETTERBOXD_STYLE.md** - Diseño del perfil

### Soluciones Implementadas:

- **SOLUCION_ACCESO_ADMIN.md** - Acceso al panel admin
- **SOLUCION_AUTOCOMPLETADO.md** - Autocompletado de navegadores
- **SOLUCION_RUTAS_TEMPORADAS_BANNERS.md** - Rutas de imágenes

---

## 🔧 MANTENIMIENTO

### Logs:
- Ubicación: `storage/logs/` y `logs/`
- Ver eventos de seguridad, errores, etc.

### Backups:
- Base de datos: `database/mmcinema3.sql`
- Migraciones: `database/migrations/`

### Tests:
- Scripts de prueba en `tests/`
- Ejecutar: `php tests/test_bd.php`

---

## 👥 ROLES Y PERMISOS

### Usuario Normal:
- Ver cartelera y películas
- Reservar entradas
- Ver series y contenido streaming
- Escribir críticas
- Gestionar perfil

### Administrador:
- Todo lo anterior +
- Gestionar películas, series, episodios
- Gestionar proyecciones y salas
- Gestionar usuarios
- Gestionar noticias
- Gestionar críticas
- Ver estadísticas

---

## 📞 SOPORTE

Para más información, consulta los archivos en `docs/` o revisa el código fuente.

**Última actualización:** <?= date('Y-m-d') ?>
