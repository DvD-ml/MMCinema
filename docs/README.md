# 📚 Documentación de MMCinema

Bienvenido a la documentación centralizada del proyecto MMCinema. Aquí encontrarás toda la información necesaria para entender, configurar y mantener el proyecto.

## 🚀 Inicio Rápido

- **[Setup Inicial](SETUP.md)** - Cómo configurar el proyecto en tu máquina local
- **[Deployment](DEPLOYMENT.md)** - Cómo desplegar a producción

## 📖 Documentación Completa

- **[Arquitectura](ARCHITECTURE.md)** - Estructura y diseño del proyecto
- **[API](API.md)** - Documentación de endpoints y funciones
- **[Solución de Problemas](TROUBLESHOOTING.md)** - Errores comunes y cómo resolverlos
- **[Configuración del Servidor](SERVER_CONFIG.md)** - Configuración pendiente del servidor

## 📋 Historial de Cambios

Ver [Changelog](changelog/) para el historial completo de versiones.

## 🔧 Configuración

### Variables de Entorno

Copia `.env.example` a `.env` y configura:

```bash
DB_HOST=localhost
DB_NAME=mmcinema_prod
DB_USER=mmcinema_user
DB_PASS=mmcinema_pass_2026
```

### Base de Datos

La base de datos se encuentra en `database/mmcinema3.sql`

## 📁 Estructura del Proyecto

```
MMCinema/
├── admin/              # Panel administrativo
├── assets/             # CSS, imágenes, JavaScript
├── backend/            # Lógica backend (PHP)
├── config/             # Configuración
├── database/           # Base de datos
├── docs/               # Documentación (este archivo)
├── helpers/            # Funciones auxiliares
├── pages/              # Páginas principales
├── scripts/            # Scripts de utilidad
├── vendor/             # Dependencias Composer
└── index.php           # Punto de entrada
```

## 🔐 Seguridad

- Todos los formularios usan tokens CSRF
- Las contraseñas se hashean con bcrypt
- Las conexiones a BD usan prepared statements
- El servidor está configurado con HTTPS

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

---

**Última actualización**: 30 de Abril de 2026
**Versión**: 1.0
