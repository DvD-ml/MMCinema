# 🚀 Setup Inicial - MMCinema

Guía para configurar MMCinema en tu máquina local.

## Requisitos Previos

- PHP 8.1 o superior
- MySQL 5.7 o superior
- Composer
- Git

## Instalación

### 1. Clonar el Repositorio

```bash
git clone <tu-repositorio> mmcinema
cd mmcinema
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
```

Edita `.env` con tus valores:

```
DB_HOST=localhost
DB_NAME=mmcinema_prod
DB_USER=root
DB_PASS=
```

### 4. Crear Base de Datos

```bash
mysql -u root -p < database/mmcinema3.sql
```

### 5. Iniciar Servidor Local

```bash
php -S localhost:8000
```

Accede a `http://localhost:8000`

## Credenciales de Prueba

- **Usuario**: admin
- **Contraseña**: admin123

## Estructura de Carpetas

- `admin/` - Panel administrativo
- `pages/` - Páginas públicas
- `backend/` - Lógica backend
- `assets/` - CSS, imágenes, JavaScript
- `config/` - Configuración
- `helpers/` - Funciones auxiliares

## Próximos Pasos

- Ver [Deployment](DEPLOYMENT.md) para desplegar a producción
- Ver [Arquitectura](ARCHITECTURE.md) para entender el proyecto
- Ver [Solución de Problemas](TROUBLESHOOTING.md) si tienes errores

---

**Última actualización**: 30 de Abril de 2026
