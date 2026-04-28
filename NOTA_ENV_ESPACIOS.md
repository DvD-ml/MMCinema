# ⚠️ Importante: Contraseñas con Espacios en .env

## 🐛 Problema Común

Si tu contraseña de aplicación de Gmail tiene espacios (como `xvzx cvwp syqf cxkk`), Dotenv no puede parsearla correctamente y obtendrás este error:

```
Fatal error: Uncaught Dotenv\Exception\InvalidFileException: 
Failed to parse dotenv file. Encountered unexpected whitespace
```

---

## ✅ Solución

**Pon la contraseña entre comillas dobles:**

### ❌ Incorrecto:
```env
MAIL_PASSWORD=xvzx cvwp syqf cxkk
```

### ✅ Correcto:
```env
MAIL_PASSWORD="xvzx cvwp syqf cxkk"
```

---

## 📋 Reglas para .env

### **Valores que NECESITAN comillas:**

1. **Contraseñas con espacios:**
   ```env
   MAIL_PASSWORD="xxxx xxxx xxxx xxxx"
   ```

2. **Valores con caracteres especiales:**
   ```env
   DB_PASS="p@ssw0rd#123"
   ```

3. **URLs con parámetros:**
   ```env
   API_URL="https://api.example.com?key=value&foo=bar"
   ```

4. **Valores con comas:**
   ```env
   ALLOWED_HOSTS="localhost,127.0.0.1,example.com"
   ```

5. **Valores con saltos de línea (usar \n):**
   ```env
   PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBg...\n-----END PRIVATE KEY-----"
   ```

### **Valores que NO necesitan comillas:**

1. **Números:**
   ```env
   MAIL_PORT=587
   MAX_UPLOAD_SIZE=10485760
   ```

2. **Booleanos:**
   ```env
   APP_DEBUG=true
   CACHE_ENABLED=false
   ```

3. **Texto sin espacios:**
   ```env
   DB_HOST=localhost
   DB_NAME=mmcinema3
   APP_ENV=development
   ```

4. **Emails:**
   ```env
   MAIL_USERNAME=david.monzonlopez@gmail.com
   ```

5. **URLs simples:**
   ```env
   BASE_URL=http://localhost/david/MMCINEMA
   ```

---

## 🔧 Cómo Arreglar el Error

### **Paso 1: Editar el archivo .env**

1. Abre: `C:\xampp\htdocs\david\MMCINEMA\.env`
2. Busca la línea: `MAIL_PASSWORD=xvzx cvwp syqf cxkk`
3. Cámbiala a: `MAIL_PASSWORD="xvzx cvwp syqf cxkk"`
4. Guarda el archivo

### **Paso 2: Verificar**

1. Ve a: `http://localhost/david/MMCINEMA/registro.php`
2. Registra un nuevo usuario
3. **Debería funcionar sin errores** ✅

---

## 📝 Ejemplo Completo de .env

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_NAME=mmcinema3
DB_USER=root
DB_PASS=

# Configuración de Correo (Gmail SMTP)
# IMPORTANTE: Contraseña con espacios entre comillas
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=david.monzonlopez@gmail.com
MAIL_PASSWORD="xvzx cvwp syqf cxkk"
MAIL_FROM_EMAIL=david.monzonlopez@gmail.com
MAIL_FROM_NAME=MMCinema

# URL Base del Proyecto
BASE_URL=http://localhost/david/MMCINEMA

# Entorno (development, production)
APP_ENV=development
```

---

## 🎓 Explicación Técnica

### ¿Por qué falla sin comillas?

Dotenv parsea el archivo línea por línea. Cuando encuentra:

```env
MAIL_PASSWORD=xvzx cvwp syqf cxkk
```

Interpreta:
- **Variable:** `MAIL_PASSWORD`
- **Valor:** `xvzx` (solo la primera palabra)
- **Error:** `cvwp syqf cxkk` son caracteres inesperados

Con comillas:

```env
MAIL_PASSWORD="xvzx cvwp syqf cxkk"
```

Interpreta:
- **Variable:** `MAIL_PASSWORD`
- **Valor:** `xvzx cvwp syqf cxkk` (todo el contenido entre comillas)

---

## 🔒 Seguridad

### **Nunca subas .env a Git:**

El archivo `.gitignore` ya incluye:

```gitignore
.env
.env.local
.env.*.local
```

### **Usa .env.example como plantilla:**

```env
# .env.example (SÍ se sube a Git)
MAIL_PASSWORD="tu_contraseña_de_aplicacion"

# .env (NO se sube a Git)
MAIL_PASSWORD="xvzx cvwp syqf cxkk"
```

---

## ✅ Checklist

Antes de reportar errores con .env, verifica:

- [ ] Las contraseñas con espacios están entre comillas
- [ ] No hay espacios antes o después del `=`
- [ ] No hay líneas vacías al final del archivo
- [ ] El archivo está en UTF-8 sin BOM
- [ ] El archivo se llama exactamente `.env` (no `.env.txt`)
- [ ] El archivo está en la raíz del proyecto
- [ ] Has copiado el archivo a XAMPP si trabajas en dos ubicaciones

---

## 🎉 Resumen

**Problema:** Error al parsear `.env` por espacios en contraseña  
**Solución:** Poner la contraseña entre comillas: `"xvzx cvwp syqf cxkk"`  
**Estado:** ✅ Arreglado

¡Ahora el registro debería funcionar correctamente!
