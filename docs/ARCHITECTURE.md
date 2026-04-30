# 🏗️ Arquitectura - MMCinema

Descripción de la arquitectura y estructura del proyecto MMCinema.

## Visión General

MMCinema es una plataforma web para gestionar películas, series y proyecciones. Utiliza una arquitectura MVC simple con PHP vanilla.

## Estructura de Carpetas

```
MMCinema/
├── admin/                  # Panel administrativo
│   ├── *.php              # Páginas de administración
│   └── includes/          # Includes compartidos
│
├── assets/                # Recursos estáticos
│   ├── css/              # Estilos CSS
│   ├── img/              # Imágenes
│   │   ├── posters/      # Posters de películas
│   │   ├── series/       # Imágenes de series
│   │   ├── noticias/     # Imágenes de noticias
│   │   ├── carrusel/     # Imágenes del carrusel
│   │   ├── logos/        # Logos
│   │   └── plataformas/  # Logos de plataformas
│   └── js/               # JavaScript
│
├── backend/               # Lógica backend
│   ├── *.php             # Controladores/acciones
│
├── components/            # Componentes reutilizables
│   ├── *.php             # Componentes PHP
│
├── config/               # Configuración
│   ├── conexion.php      # Conexión a BD
│   ├── mail.php          # Configuración de email
│
├── database/             # Base de datos
│   ├── mmcinema3.sql     # Schema de BD
│
├── docs/                 # Documentación
│   ├── README.md         # Índice
│   ├── SETUP.md          # Setup inicial
│   ├── DEPLOYMENT.md     # Deployment
│   ├── ARCHITECTURE.md   # Este archivo
│   ├── API.md            # Documentación API
│   ├── TROUBLESHOOTING.md # Solución de problemas
│   └── changelog/        # Historial de cambios
│
├── helpers/              # Funciones auxiliares
│   ├── CSRF.php          # Protección CSRF
│   ├── Logger.php        # Logging
│   ├── generar_ticket_pdf.php # Generación de PDFs
│
├── includes/             # Includes PHP
│   ├── *.php             # Includes compartidos
│
├── lib/                  # Librerías externas
│   ├── fpdf/             # Librería FPDF para PDFs
│
├── logs/                 # Logs de aplicación
│   ├── app.log           # Log principal
│
├── pages/                # Páginas públicas
│   ├── *.php             # Páginas PHP
│
├── scripts/              # Scripts de utilidad
│   ├── *.php             # Scripts PHP
│
├── storage/              # Almacenamiento
│   ├── tickets/          # PDFs de tickets
│
├── vendor/               # Dependencias Composer
│
├── .env                  # Variables de entorno
├── .env.example          # Ejemplo de .env
├── .gitignore            # Archivos ignorados por Git
├── .htaccess             # Configuración Apache
├── composer.json         # Dependencias Composer
├── composer.lock         # Lock de dependencias
├── index.php             # Punto de entrada
├── setup_complete.sh     # Script de setup
└── deploy_to_server.sh   # Script de deployment
```

## Flujo de Solicitud

```
1. Usuario accede a http://200.234.233.50
2. Apache redirige a index.php
3. index.php carga config/conexion.php
4. Se determina la página solicitada
5. Se carga la página correspondiente (pages/*.php)
6. La página puede llamar a backend/*.php para procesar datos
7. Se renderiza HTML y se envía al navegador
```

## Base de Datos

### Tablas Principales

- `usuarios` - Usuarios del sistema
- `peliculas` - Películas
- `series` - Series de TV
- `temporadas` - Temporadas de series
- `episodios` - Episodios de series
- `proyecciones` - Proyecciones de películas
- `tickets` - Tickets de entrada
- `ticket_asiento` - Asientos de tickets
- `noticias` - Noticias
- `criticas` - Críticas de películas
- `criticas_series` - Críticas de series
- `favoritos` - Películas favoritas
- `favoritos_series` - Series favoritas
- `email_queue` - Cola de emails

## Seguridad

### Protecciones Implementadas

- **CSRF**: Todos los formularios usan tokens CSRF
- **SQL Injection**: Prepared statements en todas las queries
- **XSS**: Escapado de datos en salida HTML
- **Autenticación**: Sesiones PHP con hash de contraseña
- **HTTPS**: Certificado SSL configurado

### Archivos de Seguridad

- `helpers/CSRF.php` - Generación y validación de tokens CSRF
- `config/conexion.php` - Conexión segura a BD

## Componentes Principales

### Autenticación

- `backend/login.php` - Login de usuarios
- `backend/registro.php` - Registro de usuarios
- `backend/olvide_password.php` - Recuperación de contraseña

### Gestión de Contenido

- `admin/peliculas.php` - Gestión de películas
- `admin/series.php` - Gestión de series
- `admin/noticias.php` - Gestión de noticias

### Compra de Entradas

- `pages/reservar_entradas.php` - Interfaz de compra
- `backend/crear_ticket.php` - Procesamiento de compra
- `helpers/generar_ticket_pdf.php` - Generación de PDF

## Dependencias

- **Composer**: Gestor de dependencias PHP
- **Dotenv**: Carga de variables de entorno
- **FPDF**: Generación de PDFs
- **Otros**: Ver `composer.json`

---

**Última actualización**: 30 de Abril de 2026
