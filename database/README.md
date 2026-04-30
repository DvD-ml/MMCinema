# 📊 Base de Datos - MMCinema

## Estructura

```
database/
├── mmcinema3.sql              ← ARCHIVO PRINCIPAL (usar este para importar)
├── migrations/                ← Historial de cambios (solo referencia)
│   ├── 001_agregar_columna_admin.sql
│   ├── 002_add_remember_token.sql
│   ├── 003_add_es_admin_column.sql
│   ├── 004_create_carrusel_destacado.sql
│   ├── 005_agregar_posicion_imagen.sql
│   ├── 006_create_favorito_serie.sql
│   └── agregar_categorias_carrusel.sql
└── README.md                  ← Este archivo
```

## Importar Base de Datos

### En Desarrollo (Local)

```bash
mysql -u root -p mmcinema3 < database/mmcinema3.sql
```

### En Producción (Clouding)

```bash
mysql -u mmcinema_user -p mmcinema_prod < database/mmcinema3.sql
```

## Contenido de mmcinema3.sql

El archivo `mmcinema3.sql` contiene:

- ✅ **15+ tablas** con estructura completa
- ✅ **Datos de prueba** (películas, series, episodios, usuarios, críticas)
- ✅ **Relaciones** con claves foráneas
- ✅ **Índices** para optimización
- ✅ **Charset UTF-8** para caracteres especiales

### Tablas Principales

| Tabla | Registros | Descripción |
|-------|-----------|-------------|
| usuario | 17+ | Usuarios registrados |
| pelicula | 27+ | Películas en catálogo |
| serie | 25+ | Series en catálogo |
| temporada | 43+ | Temporadas de series |
| episodio | 313+ | Episodios de series |
| critica | 26+ | Críticas de películas |
| critica_serie | 9+ | Críticas de series |
| carrusel_destacado | 3+ | Slides del carrusel |
| noticia | 12+ | Noticias del sitio |
| proyeccion | 10+ | Proyecciones de películas |
| reserva | 5+ | Reservas de entradas |
| favorito | 10+ | Películas favoritas |
| favorito_serie | 5+ | Series favoritas |
| genero | 10+ | Géneros |
| plataforma | 4+ | Plataformas (Netflix, Disney+, etc.) |
| sala_config | 5+ | Configuración de salas |

## Carpeta migrations/

Contiene el **historial de cambios** realizados durante el desarrollo:

- **NO necesitas ejecutar estos archivos** en producción
- **YA ESTÁN incluidos** en `mmcinema3.sql`
- Se mantienen como **referencia histórica**
- Útiles si necesitas entender cómo evolucionó la BD

### ⚠️ IMPORTANTE

**NO ejecutes los archivos en `migrations/` después de importar `mmcinema3.sql`**

Causarán errores como:
- "Duplicate column name"
- "Table already exists"
- "Duplicate key name"

## Backup y Restauración

### Crear Backup

```bash
mysqldump -u mmcinema_user -p mmcinema_prod > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar desde Backup

```bash
mysql -u mmcinema_user -p mmcinema_prod < backup_20260429_120000.sql
```

## Verificación

### Verificar que la BD se importó correctamente

```sql
-- Conectar a MySQL
mysql -u mmcinema_user -p mmcinema_prod

-- Ejecutar estos comandos
SHOW TABLES;
SELECT COUNT(*) FROM usuario;
SELECT COUNT(*) FROM pelicula;
SELECT COUNT(*) FROM serie;
```

### Ver tamaño de la BD

```sql
SELECT 
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size in MB'
FROM information_schema.tables
WHERE table_schema = 'mmcinema_prod';
```

## Datos de Prueba

La BD incluye datos de prueba útiles para verificar que todo funciona:

- **Usuarios**: admin, david, etc.
- **Películas**: The Batman, Deadpool, Toy Story 5, etc.
- **Series**: Game of Thrones, Breaking Bad, Better Call Saul, etc.
- **Episodios**: 313 episodios de series populares
- **Críticas**: Ejemplos de críticas de usuarios

### Limpiar Datos de Prueba (Opcional)

Si quieres una BD vacía para producción:

```sql
DELETE FROM carrusel_destacado;
DELETE FROM critica;
DELETE FROM critica_serie;
DELETE FROM episodio;
DELETE FROM temporada;
DELETE FROM pelicula;
DELETE FROM serie;
DELETE FROM usuario;
DELETE FROM noticia;
DELETE FROM proyeccion;
DELETE FROM reserva;
DELETE FROM favorito;
DELETE FROM favorito_serie;

-- Resetear auto-increment
ALTER TABLE usuario AUTO_INCREMENT = 1;
ALTER TABLE pelicula AUTO_INCREMENT = 1;
ALTER TABLE serie AUTO_INCREMENT = 1;
-- ... etc para todas las tablas
```

**Recomendación**: NO hagas esto. Los datos de prueba son útiles para verificar que todo funciona.

## Charset y Collation

Toda la BD usa:
- **Charset**: `utf8mb4`
- **Collation**: `utf8mb4_unicode_ci` o `utf8mb4_general_ci`

Esto asegura que los caracteres españoles (ñ, á, é, í, ó, ú) se guarden correctamente.

## Soporte

Si tienes problemas al importar la BD:

1. Verifica que MySQL está corriendo
2. Verifica que tienes permisos suficientes
3. Verifica que la BD `mmcinema_prod` existe
4. Verifica que el usuario `mmcinema_user` existe
5. Revisa los logs de MySQL

## Próximos Pasos

1. ✅ Importar `mmcinema3.sql` en el servidor
2. ✅ Verificar que todas las tablas se crearon
3. ✅ Verificar que los datos se importaron correctamente
4. ✅ Probar que la aplicación se conecta a la BD
5. ✅ Hacer backup de la BD

¡Listo! 🚀
