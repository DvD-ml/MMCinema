# MMCinema

Plataforma web para gestionar peliculas, series, noticias, criticas, proyecciones y compra de entradas con PDF.

## Inicio rapido

```bash
composer install
cp .env.example .env
mysql -u root -p < database/mmcinema3.sql
php -S localhost:8000
```

Acceso local: `http://localhost:8000`

## Estructura

```text
admin/       Panel administrativo
assets/      CSS, JavaScript e imagenes
backend/     Acciones de formularios y procesos internos
components/  Navbar, footer y componentes reutilizables
config/      Conexion y correo
database/    Dump principal y migraciones
helpers/     Utilidades de seguridad, PDF, logs y validacion
includes/    Utilidades compartidas
lib/         Librerias locales no instaladas por Composer
pages/       Paginas publicas
scripts/     Scripts de mantenimiento
storage/     Carpetas runtime ignoradas por git
vendor/      Dependencias de Composer
```

## Requisitos

- PHP 8.1+
- MySQL/MariaDB
- Composer
- Apache o servidor PHP compatible

## Produccion

El despliegue se hace con un unico script:

```powershell
.\deploy.ps1
```

El script empaqueta el proyecto completo, excluye secretos y archivos generados (`.env`, logs, cache y tickets PDF), lo sube al servidor y aplica las migraciones necesarias.

Antes de desplegar revisa `.env.example` y configura las variables reales directamente en el servidor. El archivo `.env` local no se sube.

## Seguridad

- Formularios protegidos con CSRF.
- Consultas preparadas con PDO.
- Passwords con `password_hash`.
- Subidas de imagen validadas y optimizadas.
- Logs y PDFs generados ignorados por git.

## Estado

Proyecto funcional, con margen de mejora en automatizacion de migraciones, pruebas y limpieza de configuracion de produccion.
