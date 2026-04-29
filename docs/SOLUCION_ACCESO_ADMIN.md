# Solución: Problema de Acceso al Panel Admin

## 🔴 PROBLEMA

Al intentar acceder a `http://localhost/david/MMCINEMA/admin/carrusel_destacado.php`, te redirige al inicio aunque tu usuario sea admin.

**Causa:** La sesión no tiene la variable `es_admin` definida correctamente.

---

## ✅ SOLUCIÓN RÁPIDA (1 minuto)

### Opción 1: Script Automático (Recomendado)

1. **Accede a este enlace:**
   ```
   http://localhost/david/MMCINEMA/actualizar_sesion_admin.php
   ```

2. **Verás un mensaje de éxito** ✅

3. **Haz clic en "Ir al Panel Carrusel"**

4. **¡Listo!** Ya puedes acceder al panel admin

---

### Opción 2: Debug Manual

Si la Opción 1 no funciona:

1. **Accede a:**
   ```
   http://localhost/david/MMCINEMA/admin/debug_sesion.php
   ```

2. **Verás información detallada** de tu sesión

3. **Si aparece el botón "Actualizar Sesión"**, haz clic

4. **Luego haz clic en "Ir al Panel Carrusel"**

---

## 🔍 VERIFICACIÓN

### Comprobar que funciona:

1. Ve a: `http://localhost/david/MMCINEMA/admin/carrusel_destacado.php`
2. **Debería mostrarte el panel** sin redirigir
3. Si funciona: ✅ Problema resuelto

---

## 🛠️ ¿POR QUÉ PASÓ ESTO?

### Causa Raíz

Cuando inicias sesión, el sistema guarda:
- ✅ `usuario_id`
- ✅ `email`
- ❌ `es_admin` (faltaba o era null)

El panel admin verifica `es_admin` y si no existe o es 0, te redirige.

### Solución Permanente

Los scripts que creé actualizan la sesión con los datos correctos de la base de datos, incluyendo el campo `es_admin`.

---

## 📋 SCRIPTS CREADOS

### 1. `actualizar_sesion_admin.php`
**Ubicación:** Raíz del proyecto
**Función:** Actualiza automáticamente la sesión del usuario actual
**Cuándo usar:** Cuando no puedes acceder al panel admin

### 2. `admin/debug_sesion.php`
**Ubicación:** Carpeta admin
**Función:** Muestra información detallada de la sesión y permite actualizarla
**Cuándo usar:** Para diagnosticar problemas de sesión

---

## 🔐 CREDENCIALES ADMIN

### Usuario Admin Principal:
- **Email:** `admin@mmcinema.com`
- **Password:** `admin123`

### Tu Usuario:
- **Email:** `david.monzonlopez@gmail.com`
- **ID:** 17
- **Es Admin:** SÍ ✅

---

## 🚨 SI SIGUE SIN FUNCIONAR

### Paso 1: Verificar en Base de Datos

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Selecciona la base de datos `mmcinema3`
3. Abre la tabla `usuario`
4. Busca tu usuario (ID 17 o email `david.monzonlopez@gmail.com`)
5. Verifica que `es_admin` = **1**

### Paso 2: Si `es_admin` es NULL o 0

Ejecuta este SQL en phpMyAdmin:

```sql
UPDATE usuario 
SET es_admin = 1 
WHERE email = 'david.monzonlopez@gmail.com';
```

### Paso 3: Actualizar Sesión

Después de actualizar la BD, ve a:
```
http://localhost/david/MMCINEMA/actualizar_sesion_admin.php
```

---

## 💡 PREVENCIÓN FUTURA

### Para evitar este problema:

1. **Siempre verifica** que el campo `es_admin` esté en la sesión al hacer login
2. **Actualiza el archivo de login** para incluir `es_admin` en la sesión
3. **Usa los scripts de debug** si tienes dudas sobre la sesión

---

## 📂 ARCHIVOS RELACIONADOS

```
C:\xampp\htdocs\david\MMCINEMA\
├── actualizar_sesion_admin.php ✅ (Script de actualización)
├── admin/
│   ├── debug_sesion.php ✅ (Script de debug)
│   ├── auth.php (Verificación de admin)
│   └── carrusel_destacado.php (Panel del carrusel)
└── backend/
    └── login.php (Proceso de login)
```

---

## ✨ RESUMEN

1. **Accede a:** `http://localhost/david/MMCINEMA/actualizar_sesion_admin.php`
2. **Haz clic en:** "Ir al Panel Carrusel"
3. **¡Listo!** Ya puedes gestionar el carrusel

---

**¿Necesitas ayuda?** Usa el script de debug para ver qué está pasando:
```
http://localhost/david/MMCINEMA/admin/debug_sesion.php
```

🚀 **¡Problema resuelto!**
