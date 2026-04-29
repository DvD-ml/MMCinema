# Sistema de Críticas Mejorado

## Cambios Implementados

### 1. Separación de Críticas por Tipo (Películas y Series)

**Ubicación**: `perfil.php`

- Se han creado **dos tabs separados** para críticas:
  - **Críticas de Películas**: Muestra solo las críticas de películas del usuario
  - **Críticas de Series**: Muestra solo las críticas de series del usuario

- Cada tab tiene su propio:
  - Grid de críticas con carrusel independiente
  - Modal independiente para ver detalles
  - Funciones JavaScript separadas

### 2. Restricción: Una Crítica por Usuario por Película/Serie

**Archivos modificados**:
- `backend/enviar_critica.php` (películas)
- `backend/enviar_critica_serie.php` (series)

**Comportamiento**:
- Si un usuario intenta escribir una segunda crítica para la misma película/serie, el sistema **actualiza la crítica existente** en lugar de crear una nueva
- Se actualiza el contenido, la puntuación y la fecha de creación
- Esto evita duplicados y mantiene la base de datos limpia

### 3. Mejoras en el Diseño

**Estilos CSS**: `css/profile.css`

- Los tabs de críticas tienen el mismo estilo que los tabs de favoritas
- Separación visual clara entre películas y series
- Carruseles independientes para cada tipo
- Modales independientes con mejor organización

### 4. Consultas SQL Optimizadas

**Películas**:
```sql
SELECT 
    c.id AS critica_id,
    c.contenido,
    c.puntuacion,
    c.creado,
    p.titulo,
    p.poster,
    p.id AS pelicula_id
FROM critica c
LEFT JOIN pelicula p ON c.id_pelicula = p.id
WHERE c.id_usuario = ?
ORDER BY c.creado DESC
```

**Series**:
```sql
SELECT 
    cs.id AS critica_id,
    cs.contenido,
    cs.puntuacion,
    cs.creado,
    s.titulo,
    s.poster,
    s.id AS serie_id
FROM critica_serie cs
LEFT JOIN serie s ON cs.id_serie = s.id
WHERE cs.id_usuario = ?
ORDER BY cs.creado DESC
```

## Funcionalidad JavaScript

### Carruseles Independientes

- `scrollCriticasPeliculas(direction)`: Controla el carrusel de películas
- `scrollCriticasSeries(direction)`: Controla el carrusel de series

### Modales Independientes

- `openCriticaPeliculaModal(index)` / `closeCriticaPeliculaModal(event)`
- `openCriticaSerieModal(index)` / `closeCriticaSerieModal(event)`

### Gestión de Tabs

- Sistema mejorado que detecta el grupo de tabs (favoritas vs críticas)
- Solo oculta/muestra secciones del mismo grupo
- Permite tener múltiples grupos de tabs en la misma página

## Estadísticas Actualizadas

El perfil ahora muestra:
- **Críticas totales**: Suma de críticas de películas + series
- **Media de valoración**: Promedio de todas las puntuaciones (películas y series)

## Rutas de Pósters

- **Películas**: `img/posters/[nombre_archivo]`
- **Series**: Ruta completa directamente desde la base de datos

## Compatibilidad

- El sistema es compatible con bases de datos que aún no tienen la tabla `critica_serie`
- Usa bloques `try-catch` para evitar errores si la tabla no existe
- Muestra mensaje apropiado si no hay críticas de ningún tipo
