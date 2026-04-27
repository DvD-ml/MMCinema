# 🔐 Sistema "Recordar Sesión" - Implementado

## ✅ ¿Qué se ha implementado?

El sistema **"Recordar sesión"** (Remember Me) permite a los usuarios mantener su sesión activa durante **30 días** sin necesidad de volver a iniciar sesión.

---

## 🎯 Características

✅ **Cookie segura** con token único  
✅ **Duración**: 30 días  
✅ **HttpOnly**: No accesible desde JavaScript (protección XSS)  
✅ **SameSite**: Protección contra CSRF  
✅ **Token único** por usuario en base de datos  
✅ **Expiración automática** de tokens antiguos  
✅ **Logging** de todas las acciones de autenticación  

---

## 📁 Archivos Creados/Modificados

### Nuevos Archivos:
- `helpers/Auth.php` - Clase de autenticación con soporte para cookies
- `sql/add_remember_token.sql` - Script SQL para agregar columnas

### Archivos Modificados:
- `backend/login.php` - Lógica de "recordar sesión"
- `login.php` - Checkbox "Recordar mi sesión"
- `logout.php` - Elimina cookies al cerrar sesión

---

## 🗄️ Cambios en Base de Datos

**IMPORTANTE:** Debes ejecutar este SQL en phpMyAdmin:

```sql
ALTER TABLE `usuario` 
ADD COLUMN `remember_token` VARCHAR(64) NULL DEFAULT NULL AFTER `reset_expira`,
ADD COLUMN `remember_expira` DATETIME NULL DEFAULT NULL AFTER `remember_token`,
ADD INDEX `idx_remember_token` (`remember_token`);
```

O importa el archivo: `sql/add_remember_token.sql`

---

## 🔧 Cómo Funciona

### 1. Usuario marca "Recordar mi sesión"

```php
// En login.php
<input type="checkbox" name="recordar" value="1">
```

### 2. Se genera un token único

```php
// En backend/login.php
$token = bin2hex(random_bytes(32)); // Token de 64 caracteres
$expira = date('Y-m-d H:i:s', strtotime('+30 days'));
```

### 3. Se guarda en BD y cookie

```php
// Guardar en BD
UPDATE usuario SET remember_token = ?, remember_expira = ? WHERE id = ?

// Crear cookie segura
setcookie('remember_token', $token, [
    'expires' => time() + (30 * 24 * 60 * 60), // 30 días
    'httponly' => true,  // No accesible desde JS
    'samesite' => 'Lax'  // Protección CSRF
]);
```

### 4. En cada página se verifica

```php
// Automático con Auth::verificarSesion()
if (!empty($_SESSION['usuario_id'])) {
    // Sesión activa
} else if (isset($_COOKIE['remember_token'])) {
    // Restaurar sesión desde cookie
}
```

---

## 💻 Uso de la Clase Auth

### Verificar si hay sesión activa:

```php
require_once "helpers/Auth.php";

if (Auth::verificarSesion($pdo)) {
    echo "Usuario autenticado";
} else {
    echo "Usuario no autenticado";
}
```

### Requerir login en una página:

```php
// Redirige a login si no está autenticado
Auth::requerirLogin($pdo);

// Con redirección personalizada
Auth::requerirLogin($pdo, 'perfil.php');
```

### Verificar rol de admin:

```php
if (Auth::tieneRol('admin')) {
    // Mostrar panel de admin
}
```

### Cerrar sesión correctamente:

```php
Auth::cerrarSesion($pdo);
// Elimina sesión, cookie y token de BD
```

---

## 🔒 Seguridad

### Protecciones Implementadas:

1. **Token único y aleatorio** (64 caracteres hexadecimales)
2. **HttpOnly cookie** - No accesible desde JavaScript (previene XSS)
3. **SameSite=Lax** - Protección contra CSRF
4. **Expiración en BD** - Tokens antiguos no funcionan
5. **Logging completo** - Todas las acciones se registran
6. **Token se elimina al logout** - No reutilizable

### Recomendaciones para Producción:

```php
// En backend/login.php, cambiar:
'secure' => true,  // Solo HTTPS (actualmente false para localhost)
```

---

## 📊 Logging

Todas las acciones se registran en `logs/app.log`:

```
[INFO] Usuario inició sesión | user_id: 5, recordar: true
[INFO] Cookie de recordar sesión creada | user_id: 5
[INFO] Sesión restaurada desde cookie | user_id: 5
[INFO] Usuario cerró sesión | user_id: 5
[SECURITY] Intento de login con contraseña incorrecta | email: test@test.com
[WARNING] Intento de restaurar sesión con token inválido
```

---

## 🧪 Pruebas

### Probar "Recordar sesión":

1. Ve a: `http://localhost/david/MMCINEMA/login.php`
2. Inicia sesión con:
   - Email: `admin@mmcinema.com`
   - Contraseña: `admin123`
   - ✅ Marca "Recordar mi sesión"
3. Cierra el navegador completamente
4. Abre el navegador de nuevo
5. Ve a: `http://localhost/david/MMCINEMA/`
6. **Deberías seguir con sesión iniciada** ✅

### Probar logout:

1. Haz clic en "Cerrar sesión"
2. Cierra el navegador
3. Abre el navegador de nuevo
4. Ve a: `http://localhost/david/MMCINEMA/`
5. **NO deberías tener sesión** ✅

---

## 🐛 Solución de Problemas

### La sesión no se mantiene:

1. Verifica que ejecutaste el SQL:
   ```sql
   SHOW COLUMNS FROM usuario LIKE 'remember%';
   ```
   Deberías ver: `remember_token` y `remember_expira`

2. Verifica que las cookies están habilitadas en el navegador

3. Revisa los logs en `logs/app.log`

### Error "Column 'remember_token' doesn't exist":

Ejecuta el archivo SQL: `sql/add_remember_token.sql` en phpMyAdmin

---

## 📝 Próximos Pasos

Ahora que tienes "Recordar sesión", puedes:

1. ✅ Continuar con Fase 2 (aplicar validación y CSRF)
2. ✅ Implementar "Recordar email" en el formulario de login
3. ✅ Agregar opción "Cerrar sesión en todos los dispositivos"

---

¡El sistema "Recordar sesión" está completamente implementado! 🎉
