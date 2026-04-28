# 📊 Instrucciones para Importar la Base de Datos

## Opción 1: Usando phpMyAdmin (Recomendado)

### Paso 1: Crear la base de datos

1. Abre phpMyAdmin: `http://localhost/phpmyadmin`
2. Haz clic en "Nueva" en el panel izquierdo
3. Nombre de la base de datos: `mmcinema3`
4. Cotejamiento: `utf8mb4_unicode_ci`
5. Haz clic en "Crear"

### Paso 2: Importar el archivo SQL

1. Selecciona la base de datos `mmcinema3` en el panel izquierdo
2. Haz clic en la pestaña "Importar"
3. Haz clic en "Seleccionar archivo"
4. Selecciona el archivo `sql/mmcinema3.sql`
5. Haz clic en "Continuar" al final de la página
6. Espera a que termine la importación

### Paso 3: Verificar la importación

1. Haz clic en la base de datos `mmcinema3`
2. Deberías ver todas las tablas:
   - `critica`
   - `critica_serie`
   - `episodio`
   - `favorito`
   - `genero`
   - `noticia`
   - `pelicula`
   - `plataforma`
   - `proyeccion`
   - `reserva`
   - `sala_config`
   - `serie`
   - `temporada`
   - `ticket`
   - `ticket_asiento`
   - `usuario`

---

## Opción 2: Usando línea de comandos

### Windows (PowerShell)

```powershell
# Navega a la carpeta del proyecto
cd C:\xampp\htdocs\MMCinema

# Importa el archivo SQL
& "C:\xampp\mysql\bin\mysql.exe" -u root -p mmcinema3 < sql/mmcinema3.sql
```

### Linux/Mac

```bash
# Navega a la carpeta del proyecto
cd /path/to/MMCinema

# Importa el archivo SQL
mysql -u root -p mmcinema3 < sql/mmcinema3.sql
```

---

## 🔑 Credenciales por Defecto

Después de importar la base de datos, tendrás un usuario administrador:

**Usuario Admin:**
- Email: `admin@mmcinema.com`
- Contraseña: `admin123`

**⚠️ IMPORTANTE:** Cambia esta contraseña inmediatamente después del primer login.

---

## ✅ Verificar que todo funciona

1. Accede a: `http://localhost/MMCinema/config/test_conexion.php`
2. Deberías ver:
   - ✅ Conexión exitosa
   - 16 tablas encontradas
   - Número de registros por tabla

---

## 🐛 Solución de Problemas

### Error: "Access denied for user 'root'@'localhost'"

**Solución:**
- Verifica que MySQL esté corriendo
- Verifica las credenciales en `.env`:
  ```env
  DB_USER=root
  DB_PASS=tu_contraseña
  ```

### Error: "Unknown database 'mmcinema3'"

**Solución:**
- Crea la base de datos primero:
  ```sql
  CREATE DATABASE mmcinema3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

### Error: "Table 'mmcinema3.usuario' doesn't exist"

**Solución:**
- La importación no se completó
- Vuelve a importar el archivo SQL
- Verifica que el archivo `sql/mmcinema3.sql` existe

### Error: "Duplicate entry" al importar

**Solución:**
- La base de datos ya tiene datos
- Elimina la base de datos y créala de nuevo:
  ```sql
  DROP DATABASE mmcinema3;
  CREATE DATABASE mmcinema3 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```
- Vuelve a importar

---

## 📝 Estructura de la Base de Datos

### Tablas Principales

- **usuario** - Usuarios del sistema (admin y usuarios normales)
- **pelicula** - Catálogo de películas
- **serie** - Catálogo de series
- **temporada** - Temporadas de series
- **episodio** - Episodios de temporadas
- **genero** - Géneros cinematográficos
- **plataforma** - Plataformas de streaming (Netflix, HBO, etc.)

### Tablas de Funcionalidad

- **proyeccion** - Horarios de películas en salas
- **ticket** - Entradas reservadas
- **ticket_asiento** - Asientos específicos de cada ticket
- **reserva** - Reservas de usuarios (legacy)
- **sala_config** - Configuración de filas/columnas por sala

### Tablas de Interacción

- **critica** - Críticas de películas
- **critica_serie** - Críticas de series
- **favorito** - Películas favoritas de usuarios
- **noticia** - Noticias del cine

---

## 🔄 Actualizar la Base de Datos

Si ya tienes datos y quieres actualizar solo la estructura:

1. Haz backup de tus datos actuales
2. Exporta desde phpMyAdmin → Exportar
3. Importa el nuevo SQL
4. Restaura tus datos si es necesario

---

## 📊 Datos de Ejemplo

El archivo SQL incluye:
- ✅ 24 películas de ejemplo
- ✅ 21 series con temporadas y episodios
- ✅ 11 noticias
- ✅ 1 usuario admin
- ✅ Géneros cinematográficos
- ✅ Plataformas de streaming
- ✅ Proyecciones de ejemplo
- ✅ Configuración de salas

---

¿Necesitas ayuda? Revisa el archivo `README.md` o `INICIO_RAPIDO.md`
