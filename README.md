# 🎬 MMCinema

Plataforma web para gestionar películas, series y proyecciones.

## 🚀 Inicio Rápido

### Desarrollo Local

```bash
# 1. Clonar repositorio
git clone <tu-repositorio> mmcinema
cd mmcinema

# 2. Instalar dependencias
composer install

# 3. Configurar variables de entorno
cp .env.example .env

# 4. Crear base de datos
mysql -u root -p < database/mmcinema3.sql

# 5. Iniciar servidor
php -S localhost:8000
```

Accede a `http://localhost:8000`

### Producción

Ver [Deployment](docs/DEPLOYMENT.md) para desplegar a un servidor.

## 📚 Documentación

- **[Setup Inicial](docs/SETUP.md)** - Cómo configurar el proyecto
- **[Deployment](docs/DEPLOYMENT.md)** - Cómo desplegar a producción
- **[Arquitectura](docs/ARCHITECTURE.md)** - Estructura del proyecto
- **[API](docs/API.md)** - Documentación de endpoints
- **[Solución de Problemas](docs/TROUBLESHOOTING.md)** - Errores comunes
- **[Configuración del Servidor](docs/SERVER_CONFIG.md)** - Tareas pendientes

## 🎯 Características

- ✅ Gestión de películas y series
- ✅ Sistema de proyecciones
- ✅ Compra de entradas con PDF
- ✅ Panel administrativo
- ✅ Sistema de críticas y reseñas
- ✅ Favoritos
- ✅ Noticias
- ✅ Autenticación segura
- ✅ Protección CSRF

## 🔒 Seguridad

- Tokens CSRF en todos los formularios
- Prepared statements para prevenir SQL injection
- Contraseñas hasheadas con bcrypt
- HTTPS configurado
- Sesiones PHP seguras

## 📁 Estructura

```
MMCinema/
├── admin/          # Panel administrativo
├── assets/         # CSS, imágenes, JavaScript
├── backend/        # Lógica backend
├── config/         # Configuración
├── database/       # Base de datos
├── docs/           # Documentación
├── helpers/        # Funciones auxiliares
├── pages/          # Páginas públicas
├── scripts/        # Scripts de utilidad
├── storage/        # Almacenamiento
└── vendor/         # Dependencias
```

## 🛠️ Requisitos

- PHP 8.1+
- MySQL 5.7+
- Composer
- Apache 2.4+

## 📊 Estado del Servidor

**Servidor**: 200.234.233.50
**OS**: Ubuntu 22.04 LTS
**Estado**: ✅ Funcional

### ✅ Configurado

- Apache 2.4
- PHP 8.1
- MySQL
- HTTPS (certificado autofirmado)
- CSRF tokens
- Permisos correctos

### ⚠️ Pendiente

- Dominio personalizado
- Certificado SSL válido
- Firewall
- Backups automáticos
- Emails

Ver [Configuración del Servidor](docs/SERVER_CONFIG.md) para más detalles.

## 📞 Contacto

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

## 📄 Licencia

Todos los derechos reservados.

---

**Última actualización**: 30 de Abril de 2026
**Versión**: 1.0
