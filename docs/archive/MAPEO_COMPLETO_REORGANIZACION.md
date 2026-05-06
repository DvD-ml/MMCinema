# 📋 MAPEO COMPLETO DE REORGANIZACIÓN - TODOS LOS CAMBIOS

## Status: ANÁLISIS EXHAUSTIVO COMPLETADO

**Fecha**: May 4, 2026
**Archivos Analizados**: 45+
**Cambios Identificados**: 200+
**Complejidad**: ALTA

---

## 📊 RESUMEN DE CAMBIOS POR TIPO

### 1. Cambios en Includes (require_once)
- **Archivos afectados**: 20+
- **Cambios**: `admin/includes/` → `admin/helpers/`
- **Ejemplo**:
  ```php
  // ANTES
  require_once __DIR__ . "/includes/upload_helper.php";
  
  // DESPUÉS
  require_once __DIR__ . "/helpers/upload_helper.php";
  ```

### 2. Cambios en admin_header.php
- **Archivos afectados**: 45 (todos incluyen admin_header.php)
- **Cambios**: Actualizar todos los links de navegación
- **Ejemplo**:
  ```php
  // ANTES
  <a href="peliculas.php">Películas</a>
  
  // DESPUÉS
  <a href="pages/peliculas/list.php">Películas</a>
  ```

### 3. Cambios en Links de Navegación
- **Archivos afectados**: 30+
- **Cambios**: Actualizar href en todos los botones y links
- **Ejemplo**:
  ```php
  // ANTES
  <a href="pelicula_form.php?id=1">Editar</a>
  
  // DESPUÉS
  <a href="pages/peliculas/form.php?id=1">Editar</a>
  ```

### 4. Cambios en Form Actions
- **Archivos afectados**: 30+
- **Cambios**: Actualizar action en todos los formularios
- **Ejemplo**:
  ```php
  // ANTES
  <form action="pelicula_guardar.php" method="POST">
  
  // DESPUÉS
  <form action="pages/peliculas/save.php" method="POST">
  ```

### 5. Cambios en Redirects (header Location)
- **Archivos afectados**: 30+
- **Cambios**: Actualizar header Location en todos los save/delete
- **Ejemplo**:
  ```php
  // ANTES
  header("Location: peliculas.php?ok=1");
  
  // DESPUÉS
  header("Location: pages/peliculas/list.php?ok=1");
  ```

---

## 🗂️ ESTRUCTURA DE CARPETAS NUEVA

```
admin/
├── pages/
│   ├── dashboard/
│   │   ├── index.php (admin/index.php)
│   │   ├── carrusel_destacado.php
│   │   └── series_panel.php
│   ├── peliculas/
│   │   ├── list.php (peliculas.php)
│   │   ├── form.php (pelicula_form.php)
│   │   ├── save.php (pelicula_guardar.php)
│   │   └── delete.php (pelicula_borrar.php)
│   ├── noticias/
│   │   ├── list.php (noticias.php)
│   │   ├── form.php (noticia_form.php)
│   │   ├── save.php (noticia_guardar.php)
│   │   └── delete.php (noticia_borrar.php)
│   ├── proyecciones/
│   │   ├── list.php (proyecciones.php)
│   │   ├── form.php (proyeccion_form.php)
│   │   ├── save.php (proyeccion_guardar.php)
│   │   ├── delete.php (proyeccion_borrar.php)
│   │   └── api.php (proyecciones_api.php)
│   ├── salas/
│   │   ├── list.php (salas.php)
│   │   ├── form.php (sala_form.php)
│   │   ├── save.php (sala_guardar.php)
│   │   └── delete.php (sala_borrar.php)
│   ├── usuarios/
│   │   ├── list.php (usuarios.php)
│   │   ├── form.php (usuario_form.php)
│   │   ├── save.php (usuario_guardar.php)
│   │   └── delete.php (usuario_borrar.php)
│   └── series/
│       ├── list.php (series.php)
│       ├── form.php (serie_form.php)
│       ├── save.php (serie_guardar.php)
│       ├── delete.php (serie_borrar.php)
│       ├── panel.php (series_panel.php)
│       ├── temporadas/
│       │   ├── list.php (temporadas.php)
│       │   ├── form.php (temporada_form.php)
│       │   ├── save.php (temporada_guardar.php)
│       │   └── delete.php (temporada_borrar.php)
│       ├── episodios/
│       │   ├── list.php (episodios.php)
│       │   ├── form.php (episodio_form.php)
│       │   ├── save.php (episodio_guardar.php)
│       │   └── delete.php (episodio_borrar.php)
│       └── criticas/
│           ├── list.php (criticas_series.php)
│           ├── form.php (critica_form.php)
│           ├── save.php (critica_guardar.php)
│           └── delete.php (critica_borrar.php)
├── helpers/
│   ├── upload_helper.php (admin/includes/upload_helper.php)
│   └── series_admin_ui.php (admin/includes/series_admin_ui.php)
├── crud/
│   ├── delete.php
│   ├── form.php
│   └── save.php
├── admin_header.php
├── auth.php
└── index.php (dashboard)
```

---

## 📝 CAMBIOS DETALLADOS POR ARCHIVO

### admin_header.php
**Cambios**: Actualizar TODOS los links de navegación

**Links a cambiar**:
```php
// ANTES
<a href="index.php">Dashboard</a>
<a href="peliculas.php">Películas</a>
<a href="noticias.php">Noticias</a>
<a href="proyecciones.php">Proyecciones</a>
<a href="salas.php">Salas</a>
<a href="usuarios.php">Usuarios</a>
<a href="series.php">Series</a>
<a href="carrusel_destacado.php">Carrusel</a>

// DESPUÉS
<a href="pages/dashboard/index.php">Dashboard</a>
<a href="pages/peliculas/list.php">Películas</a>
<a href="pages/noticias/list.php">Noticias</a>
<a href="pages/proyecciones/list.php">Proyecciones</a>
<a href="pages/salas/list.php">Salas</a>
<a href="pages/usuarios/list.php">Usuarios</a>
<a href="pages/series/list.php">Series</a>
<a href="pages/dashboard/carrusel_destacado.php">Carrusel</a>
```

### admin/pages/peliculas/list.php (peliculas.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/admin_header.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../admin_header.php";
   ```

2. Links:
   ```php
   // ANTES
   <a href="pelicula_form.php" class="btn btn-primary">Añadir película</a>
   <a href="pelicula_form.php?id=1">Editar</a>
   <a href="index.php">Volver</a>
   
   // DESPUÉS
   <a href="form.php" class="btn btn-primary">Añadir película</a>
   <a href="form.php?id=1">Editar</a>
   <a href="../../pages/dashboard/index.php">Volver</a>
   ```

3. Form actions:
   ```php
   // ANTES
   <form action="pelicula_borrar.php" method="POST">
   
   // DESPUÉS
   <form action="delete.php" method="POST">
   ```

### admin/pages/peliculas/form.php (pelicula_form.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/admin_header.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../admin_header.php";
   ```

2. Form action:
   ```php
   // ANTES
   <form action="pelicula_guardar.php" method="POST">
   
   // DESPUÉS
   <form action="save.php" method="POST">
   ```

3. Links:
   ```php
   // ANTES
   <a href="peliculas.php">Volver</a>
   
   // DESPUÉS
   <a href="list.php">Volver</a>
   ```

### admin/pages/peliculas/save.php (pelicula_guardar.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/includes/optimizar_imagen.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../includes/optimizar_imagen.php";
   ```

2. Redirects:
   ```php
   // ANTES
   header("Location: peliculas.php?error=imagen");
   header("Location: peliculas.php?ok=1");
   
   // DESPUÉS
   header("Location: list.php?error=imagen");
   header("Location: list.php?ok=1");
   ```

### admin/pages/peliculas/delete.php (pelicula_borrar.php)
**Cambios**:
1. Redirects:
   ```php
   // ANTES
   header("Location: peliculas.php?error=1");
   header("Location: peliculas.php?ok=1");
   
   // DESPUÉS
   header("Location: list.php?error=1");
   header("Location: list.php?ok=1");
   ```

### admin/pages/series/list.php (series.php)
**Cambios**:
1. Links:
   ```php
   // ANTES
   <a href="serie_form.php">+ Añadir serie</a>
   <a href="serie_form.php?id=1">Editar</a>
   <a href="temporadas.php?id_serie=1">Temporadas</a>
   <a href="series_panel.php">Resumen</a>
   <a href="criticas_series.php">Críticas</a>
   
   // DESPUÉS
   <a href="form.php">+ Añadir serie</a>
   <a href="form.php?id=1">Editar</a>
   <a href="temporadas/list.php?id_serie=1">Temporadas</a>
   <a href="panel.php">Resumen</a>
   <a href="criticas/list.php">Críticas</a>
   ```

2. Form actions:
   ```php
   // ANTES
   <form action="serie_borrar.php" method="POST">
   
   // DESPUÉS
   <form action="delete.php" method="POST">
   ```

### admin/pages/series/temporadas/list.php (temporadas.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/includes/series_admin_ui.php";
   require_once __DIR__ . "/admin_header.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../../helpers/series_admin_ui.php";
   require_once __DIR__ . "/../../../admin_header.php";
   ```

2. Links:
   ```php
   // ANTES
   <a href="temporada_form.php?id_serie=1">+ Añadir temporada</a>
   <a href="temporada_form.php?id=1">Editar</a>
   <a href="episodios.php?id_temporada=1">Episodios</a>
   <a href="series_panel.php">Resumen</a>
   
   // DESPUÉS
   <a href="form.php?id_serie=1">+ Añadir temporada</a>
   <a href="form.php?id=1">Editar</a>
   <a href="../episodios/list.php?id_temporada=1">Episodios</a>
   <a href="../panel.php">Resumen</a>
   ```

3. Form actions:
   ```php
   // ANTES
   <form action="temporada_borrar.php" method="POST">
   
   // DESPUÉS
   <form action="delete.php" method="POST">
   ```

### admin/pages/series/temporadas/form.php (temporada_form.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/includes/series_admin_ui.php";
   require_once __DIR__ . "/admin_header.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../../helpers/series_admin_ui.php";
   require_once __DIR__ . "/../../../admin_header.php";
   ```

2. Form action:
   ```php
   // ANTES
   <form action="temporada_guardar.php" method="POST">
   
   // DESPUÉS
   <form action="save.php" method="POST">
   ```

3. Links:
   ```php
   // ANTES
   <a href="temporadas.php?id_serie=1">Volver</a>
   <a href="series_panel.php">Resumen</a>
   
   // DESPUÉS
   <a href="list.php?id_serie=1">Volver</a>
   <a href="../panel.php">Resumen</a>
   ```

### admin/pages/series/temporadas/save.php (temporada_guardar.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/includes/upload_helper.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../../helpers/upload_helper.php";
   ```

2. Redirects:
   ```php
   // ANTES
   header("Location: temporadas.php?error=1");
   header("Location: temporadas.php?id_serie=1&ok=1");
   
   // DESPUÉS
   header("Location: list.php?error=1");
   header("Location: list.php?id_serie=1&ok=1");
   ```

### admin/pages/series/temporadas/delete.php (temporada_borrar.php)
**Cambios**:
1. Redirects:
   ```php
   // ANTES
   header("Location: temporadas.php?error=1");
   header("Location: temporadas.php?id_serie=1&ok=1");
   
   // DESPUÉS
   header("Location: list.php?error=1");
   header("Location: list.php?id_serie=1&ok=1");
   ```

### admin/pages/series/episodios/list.php (episodios.php)
**Cambios**: Similar a temporadas

### admin/pages/series/episodios/form.php (episodio_form.php)
**Cambios**: Similar a temporadas

### admin/pages/series/episodios/save.php (episodio_guardar.php)
**Cambios**: Similar a temporadas

### admin/pages/series/episodios/delete.php (episodio_borrar.php)
**Cambios**: Similar a temporadas

### admin/pages/series/criticas/list.php (criticas_series.php)
**Cambios**:
1. Includes:
   ```php
   // ANTES
   require_once __DIR__ . "/includes/series_admin_ui.php";
   
   // DESPUÉS
   require_once __DIR__ . "/../../../helpers/series_admin_ui.php";
   ```

2. Links:
   ```php
   // ANTES
   <a href="critica_form.php?tipo=serie">+ Añadir crítica</a>
   <a href="critica_form.php?id=1&tipo=serie">Editar</a>
   
   // DESPUÉS
   <a href="form.php?tipo=serie">+ Añadir crítica</a>
   <a href="form.php?id=1&tipo=serie">Editar</a>
   ```

### admin/pages/criticas/list.php (criticas.php)
**Cambios**:
1. Links:
   ```php
   // ANTES
   <a href="critica_form.php?tipo=pelicula">+ Añadir crítica</a>
   <a href="critica_form.php?id=1&tipo=pelicula">Editar</a>
   
   // DESPUÉS
   <a href="form.php?tipo=pelicula">+ Añadir crítica</a>
   <a href="form.php?id=1&tipo=pelicula">Editar</a>
   ```

---

## 📊 RESUMEN DE CAMBIOS

| Tipo de Cambio | Cantidad | Archivos Afectados |
|---|---|---|
| Cambios en includes | 20+ | 20+ |
| Cambios en links | 100+ | 30+ |
| Cambios en form actions | 30+ | 30+ |
| Cambios en redirects | 50+ | 30+ |
| **Total** | **200+** | **45+** |

---

## ✅ PLAN DE IMPLEMENTACIÓN

### Fase 1: Crear Estructura (5 min)
1. Crear carpeta `admin/pages/`
2. Crear subcarpetas por entidad
3. Crear carpeta `admin/helpers/`

### Fase 2: Mover Archivos (10 min)
1. Mover archivos a nuevas carpetas
2. Renombrar archivos
3. Mover `admin/includes/` a `admin/helpers/`

### Fase 3: Actualizar Includes (15 min)
1. Actualizar rutas en todos los archivos
2. Verificar que no hay errores

### Fase 4: Actualizar Links (20 min)
1. Actualizar admin_header.php
2. Actualizar todos los links en páginas
3. Actualizar todos los form actions
4. Actualizar todos los redirects

### Fase 5: Verificación (15 min)
1. Probar todos los links
2. Probar todos los formularios
3. Probar todos los CRUD operations
4. Verificar que no hay errores 404

---

**Status**: MAPEO COMPLETO LISTO
**Próximo**: Implementación de reorganización
**Fecha**: May 4, 2026

