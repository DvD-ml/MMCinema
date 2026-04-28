# 🚀 Inicio Rápido - MMCinema

Guía rápida para poner en marcha el proyecto en **5 minutos**.

---

## ⚡ Pasos Rápidos

### 1️⃣ Instalar Dependencias (30 segundos)

```bash
cd MMCinema
composer install
```

### 2️⃣ Configurar Variables de Entorno (2 minutos)

```bash
# Copiar archivo de ejemplo
copy .env.example .env
```

Edita `.env` y cambia:
- `DB_PASS=` (tu contraseña de MySQL)
- `MAIL_USERNAME=` (tu email de Gmail)
- `MAIL_PASSWORD=` (contraseña de aplicación de Gmail)
- `BASE_URL=` (ej: `http://localhost/MMCinema`)

### 3️⃣ Crear Base de Datos (1 minuto)

Abre phpMyAdmin y ejecuta:

```sql
CREATE DATABASE mmcinema3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Luego importa el archivo SQL (si lo tienes):
- Ir a phpMyAdmin → mmcinema3 → Importar → Seleccionar archivo SQL

### 4️⃣ Iniciar Servidor (10 segundos)

#### Con XAMPP/WAMP:
1. Inicia Apache y MySQL
2. Ve a: `http://localhost/MMCinema`

#### Con servidor PHP integrado:
```bash
php -S localhost:8000
```
Ve a: `http://localhost:8000`

### 5️⃣ Verificar Instalación (30 segundos)

Accede a: `http://localhost/MMCinema/config/test_conexion.php`

Si ves "✅ Conexión exitosa", ¡todo listo!

---

## 🎯 Accesos

### Usuario Normal
- URL: `http://localhost/MMCinema`
- Regístrate desde la página

### Panel Admin
- URL: `http://localhost/MMCinema/admin`
- Email: `admin@mmcinema.com`
- Contraseña: `admin123` (cámbiala después)

---

## 🐛 Problemas Comunes

### "Class 'Dotenv\Dotenv' not found"
```bash
composer install
```

### "Access denied for user"
Verifica `DB_USER` y `DB_PASS` en `.env`

### "Table doesn't exist"
Importa el archivo SQL de la base de datos

### Correos no se envían
1. Usa contraseña de aplicación de Gmail (no tu contraseña normal)
2. Activa verificación en 2 pasos en Google
3. Genera contraseña en: https://myaccount.google.com/apppasswords

---

## 📚 Más Información

- [README completo](README.md) - Documentación detallada
- [Mejoras implementadas](MEJORAS_IMPLEMENTADAS.md) - Changelog
- [Test de conexión](http://localhost/MMCinema/config/test_conexion.php) - Verificar BD

---

¡Listo! 🎉 Tu proyecto está funcionando.
