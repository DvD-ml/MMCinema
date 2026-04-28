# 🎬 MMCinema - Resumen Completo del Proyecto

## 📌 ¿Qué es MMCinema?

MMCinema es una **plataforma web completa de gestión de cine digital** que permite:
- Explorar películas en cartelera y próximos estrenos
- Navegar por series de TV con temporadas y episodios
- Reservar entradas con selección de asientos
- Escribir y leer críticas de películas y series
- Gestionar favoritas y listas personales
- Leer noticias del mundo del cine
- Panel administrativo completo para gestión de contenido

---

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.4+ con PDO
- **Base de Datos:** MySQL/MariaDB (`mmcinema3`)
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Librerías:** PHPMailer (correos), Composer (dependencias), PHPDotenv (variables de entorno)
- **Seguridad:** CSRF protection, validación centralizada, logging, variables de entorno

---

## 📁 Archivos de Documentación

| Archivo | Descripción |
|---------|-------------|
| `README.md` | Documentación completa y detallada |
| `INICIO_RAPIDO.md` | Guía rápida para iniciar en 5 minutos |
| `INSTRUCCIONES_BD.md` | Cómo importar la base de datos |
| `MEJORAS_IMPLEMENTADAS.md` | Changelog de mejoras de seguridad |
| `RESUMEN_COMPLETO.md` | Este archivo - resumen general |

---

## 🚀 Inicio Rápido (5 pasos)

### 1️⃣ Instalar Dependencias
```bash
composer install
```

### 2️⃣ Configurar Variables de Entorno
```bash
copy .env.example .env
# Edita .env con tus credenciales
```

### 3️⃣ Crear Base de Datos
```sql
CREATE DATABASE mmcinema3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4️⃣ Importar SQL
- Abre phpMyAdmin
- Selecciona `mmcinema3`
- Importa `sql/mmcinema3.sql`

### 5️⃣ Iniciar Servidor
- Con XAMPP: Inicia Apache + MySQL
- Accede a: `http://localhost/MMCinema`

---

## 🔑 Credenciales por Defecto

**Panel Admin:**
- URL: `http://localhost/MMCinema/admin`
- Email: `admin@mmcinema.com`
- Contraseña: `admin123`

⚠️ **Cambia esta contraseña después del primer login**

---

## ✅ Verificar Instalación

Accede a: `http://localhost/MMCinema/config/test_conexion.php`

Deberías ver:
- ✅ Conexión exitosa a BD
- ✅ 16 tablas encontradas
- ✅ Número de registros por tabla

---

## 📊 Estructura de la Base de Datos

### Tablas Principales (16 tablas)

| Tabla | Descripción | Registros de Ejemplo |
|-------|-------------|---------------------|
| `usuario` | Usuarios del sistema | 1 admin |
| `pelicula` | Catálogo de películas | 24 películas |
| `serie` | Catálogo de series | 21 series |
| `temporada` | Temporadas de series | 44 temporadas |
| `episodio` | Episodios | 332 episodios |
| `genero` | Géneros cinematográficos | 11 géneros |
| `plataforma` | Plataformas streaming | 4 plataformas |
| `proyeccion` | Horarios de películas | Varios horarios |
| `ticket` | Entradas reservadas | Ejemplos |
| `ticket_asiento` | Asientos por ticket | Ejemplos |
| `sala_config` | Config de salas | 6 salas |
| `critica` | Críticas de películas | Ejemplos |
| `critica_serie` | Críticas de series | Ejemplos |
| `favorito` | Favoritas de usuarios | Ejemplos |
| `noticia` | Noticias del cine | 11 noticias |
| `reserva` | Reservas (legacy) | Ejemplos |

---

## 🔐 Mejoras de Seguridad Implementadas

### ✅ Fase 1 Completada

1. **Variables de Entorno (.env)**
   - Credenciales fuera del código
   - Archivo `.env` protegido en `.gitignore`

2. **Validación Centralizada**
   - Clase `Validator` para validar inputs
   - Métodos para IDs, emails, fechas, puntuaciones, etc.

3. **Protección CSRF**
   - Clase `CSRF` con tokens seguros
   - Helper para formularios

4. **Logging Centralizado**
   - Clase `Logger` con niveles INFO, WARNING, ERROR, DEBUG, SECURITY
   - Logs con timestamp, IP, usuario

5. **Gestión de Errores Mejorada**
   - Errores de BD registrados en logs
   - Mensajes genéricos en producción

---

## 📂 Estructura del Proyecto

```
MMCinema/
├── admin/                    # Panel administrativo
│   ├── index.php            # Dashboard
│   ├── peliculas.php        # Gestión de películas
│   ├── series.php           # Gestión de series
│   ├── noticias.php         # Gestión de noticias
│   └── usuarios.php         # Gestión de usuarios
├── backend/                 # Lógica de negocio
│   ├── login.php           # Autenticación
│   ├── registro.php        # Registro de usuarios
│   ├── reservar.php        # Reserva de entradas
│   └── enviar_critica.php  # Envío de críticas
├── config/                  # Configuración
│   ├── conexion.php        # Conexión a BD (con .env)
│   ├── mail.php            # Config de correos (con .env)
│   └── test_conexion.php   # Test de conexión
├── helpers/                 # Clases auxiliares
│   ├── Validator.php       # Validación centralizada
│   ├── CSRF.php            # Protección CSRF
│   └── Logger.php          # Logging
├── css/                     # Estilos
├── img/                     # Imágenes
├── logs/                    # Logs de la aplicación
├── cache/                   # Caché de datos
├── tickets/                 # PDFs de entradas
├── sql/                     # Base de datos
│   └── mmcinema3.sql       # Esquema completo
├── vendor/                  # Dependencias Composer
├── .env                     # Variables de entorno (NO SUBIR A GIT)
├── .env.example            # Plantilla de variables
├── .gitignore              # Archivos ignorados por Git
├── composer.json           # Dependencias PHP
├── index.php               # Página principal
├── pelicula.php            # Detalle de película
├── serie.php               # Detalle de serie
├── cartelera.php           # Listado de películas
├── series.php              # Listado de series
├── perfil.php              # Perfil de usuario
├── login.php               # Login
├── registro.php            # Registro
└── README.md               # Documentación principal
```

---

## 🎯 Funcionalidades Principales

### Para Usuarios

✅ **Explorar Contenido**
- Cartelera de películas con filtros
- Próximos estrenos
- Catálogo de series con temporadas y episodios
- Noticias del cine

✅ **Interactuar**
- Escribir críticas con puntuación (1-5 estrellas)
- Guardar favoritas
- Ver puntuaciones promedio

✅ **Reservar Entradas**
- Seleccionar proyección (película, fecha, hora, sala)
- Elegir asientos en mapa interactivo
- Generar ticket con código único
- Descargar entrada en PDF
- Recibir confirmación por email

✅ **Gestionar Perfil**
- Ver historial de tickets
- Ver críticas personales
- Gestionar favoritas

### Para Administradores

✅ **Panel Administrativo**
- Dashboard con estadísticas
- CRUD de películas
- CRUD de series, temporadas y episodios
- CRUD de noticias
- CRUD de usuarios
- Gestión de críticas
- Gestión de proyecciones

---

## 🔧 Configuración Necesaria

### Variables de Entorno (.env)

```env
# Base de Datos
DB_HOST=localhost
DB_NAME=mmcinema3
DB_USER=root
DB_PASS=

# Correo (Gmail SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_de_aplicacion
MAIL_FROM_EMAIL=tu_email@gmail.com
MAIL_FROM_NAME=MMCinema

# URL Base
BASE_URL=http://localhost/MMCinema

# Entorno
APP_ENV=development
```

### Contraseña de Aplicación de Gmail

1. Ve a tu cuenta de Google → Seguridad
2. Activa verificación en 2 pasos
3. Ve a "Contraseñas de aplicaciones"
4. Genera una nueva para "Correo"
5. Copia esa contraseña en `MAIL_PASSWORD`

---

## 🐛 Solución de Problemas Comunes

### "Class 'Dotenv\Dotenv' not found"
```bash
composer install
```

### "Access denied for user 'root'@'localhost'"
- Verifica credenciales en `.env`
- Verifica que MySQL esté corriendo

### "Table 'mmcinema3.pelicula' doesn't exist"
- Importa el archivo `sql/mmcinema3.sql`

### "Failed to send email"
- Usa contraseña de aplicación de Gmail
- Activa verificación en 2 pasos

### Las imágenes no se muestran
- Verifica permisos de carpetas `img/` y `assets/`
- Verifica `BASE_URL` en `.env`

---

## 📝 Próximas Mejoras (Fase 2)

### Pendientes de Implementar

1. **Aplicar validación en archivos existentes**
   - Actualizar `pelicula.php`, `serie.php`, etc.
   - Usar `Validator::validarId()` en lugar de `(int)$_GET['id']`

2. **Agregar CSRF a todos los formularios**
   - Login, registro, críticas, reservas
   - Formularios del admin

3. **Crear repositorios de BD**
   - `PeliculaRepository.php`
   - `SerieRepository.php`
   - `UsuarioRepository.php`

4. **Implementar caché**
   - Caché de carrusel
   - Caché de series populares

5. **API REST básica**
   - `/api/favorito.php`
   - `/api/critica.php`

---

## 📚 Recursos Adicionales

- [PHPDotenv Documentation](https://github.com/vlucas/phpdotenv)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [PHP Filter Functions](https://www.php.net/manual/en/book.filter.php)
- [PHPMailer Documentation](https://github.com/PHPMailer/PHPMailer)

---

## 👨‍💻 Autor

David Monzón López

---

## 📞 Soporte

Si tienes problemas:
1. Revisa la documentación correspondiente
2. Verifica los logs en `logs/app.log`
3. Usa el script de test: `config/test_conexion.php`
4. Contacta al desarrollador

---

## 🎉 ¡Listo para Usar!

Tu proyecto MMCinema está completamente configurado y listo para funcionar. Disfruta explorando todas sus funcionalidades.

**Accesos rápidos:**
- 🏠 Inicio: `http://localhost/MMCinema`
- 🎬 Cartelera: `http://localhost/MMCinema/cartelera.php`
- 📺 Series: `http://localhost/MMCinema/series.php`
- 👤 Perfil: `http://localhost/MMCinema/perfil.php`
- 🔧 Admin: `http://localhost/MMCinema/admin`
- ✅ Test BD: `http://localhost/MMCinema/config/test_conexion.php`
