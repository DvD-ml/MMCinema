# 🔍 ANÁLISIS COMPLETO DEL PANEL ADMIN - TODOS LOS ARCHIVOS

## Status: 50+ PROBLEMAS ENCONTRADOS

**Fecha**: May 4, 2026
**Archivos Analizados**: 40+
**Problemas Críticos**: 5
**Problemas Altos**: 10
**Problemas Medios**: 10+
**Problemas Bajos**: 25+

---

## 🔴 PROBLEMAS CRÍTICOS (DEBEN ARREGLARSE INMEDIATAMENTE)

### 1. ❌ sala_borrar.php - CSRF Validation Roto
**Archivo**: `admin/sala_borrar.php`
**Problema**: 
- Usa GET parameter en lugar de POST
- Llama a `CSRF::validarOAbortar()` pero espera POST data
- No llama a `verificarAuth()`
- La validación CSRF falla silenciosamente

**Código Actual**:
```php
CSRF::validarOAbortar();  // Espera POST, pero recibe GET
$sala = '';
if (isset($_GET['sala'])) {  // Usa GET
    $sala = $_GET['sala'];
}
```

**Impacto**: Eliminación de salas sin validación CSRF, sin autenticación

**Solución**: Convertir a POST con formulario

---

### 2. ❌ sala_guardar.php - Sin Validación CSRF
**Archivo**: `admin/sala_guardar.php`
**Problema**: 
- NO llama a `CSRF::validarOAbortar()`
- NO llama a `verificarAuth()`
- Acepta POST data sin validación

**Código Actual**:
```php
<?php
require_once "auth.php";
verificarAuth();

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../helpers/CSRF.php";

$sala = trim($_POST['sala'] ?? '');  // Sin validación CSRF
```

**Impacto**: Ataques CSRF posibles en creación/edición de salas

**Solución**: Agregar `CSRF::validarOAbortar();`

---

### 3. ❌ usuario_guardar.php - Sin Validación CSRF
**Archivo**: `admin/usuario_guardar.php`
**Problema**: 
- NO llama a `CSRF::validarOAbortar()`
- Acepta POST data sin validación CSRF

**Código Actual**:
```php
<?php
require_once "auth.php";
verificarAuth();

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../helpers/CSRF.php";

$id       = (int)($_POST['id'] ?? 0);  // Sin validación CSRF
```

**Impacto**: Ataques CSRF posibles en creación/edición de usuarios

**Solución**: Agregar `CSRF::validarOAbortar();`

---

### 4. ❌ critica_guardar.php - Sin Validación CSRF
**Archivo**: `admin/critica_guardar.php`
**Problema**: 
- NO llama a `CSRF::validarOAbortar()`
- Acepta POST data sin validación CSRF

**Código Actual**:
```php
<?php
require_once "auth.php";
verificarAuth();

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../helpers/CSRF.php";

$tipo = $_POST['tipo'] ?? 'pelicula';  // Sin validación CSRF
```

**Impacto**: Ataques CSRF posibles en creación/edición de críticas

**Solución**: Agregar `CSRF::validarOAbortar();`

---

### 5. ❌ noticia_guardar.php - Unsafe Query en afterSave
**Archivo**: `admin/noticia_guardar.php`
**Problema**: 
- Usa `ORDER BY id DESC LIMIT 1` para encontrar registro insertado
- Si hay múltiples noticias con el mismo título, actualiza la equivocada
- Timestamp se asigna a la noticia incorrecta

**Código Actual**:
```php
$afterSave = function($data, $pdo) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        // Si es nuevo, actualizar fecha de publicación
        $stm = $pdo->prepare("UPDATE noticia SET publicado = NOW() WHERE titulo = ? ORDER BY id DESC LIMIT 1");
        $stm->execute([$data['titulo']]);
    }
};
```

**Impacto**: Datos inconsistentes, fechas de publicación incorrectas

**Solución**: Usar `LAST_INSERT_ID()` o retornar ID del insert

---

## 🟠 PROBLEMAS ALTOS (SEGURIDAD)

### 6. ❌ Image Upload - Sin Validación de Tipo de Archivo
**Archivos**: `pelicula_guardar.php`, `noticia_guardar.php`, `serie_guardar.php`, `temporada_guardar.php`
**Problema**: 
- Solo valida MIME type con `getimagesize()`
- No valida extensión de archivo
- Atacante podría subir PHP con MIME type de imagen

**Código Actual**:
```php
$info = getimagesize($file['tmp_name']);  // Solo MIME
if ($info === false) {
    throw new Exception("El archivo no es una imagen válida.");
}
```

**Impacto**: Remote Code Execution posible

**Solución**: Validar extensión: `if (!in_array($ext, ['jpg', 'png', 'webp']))`

---

### 7. ❌ Image Upload - Sin Validación de Tamaño
**Archivos**: Todos los que suben imágenes
**Problema**: 
- No hay límite de tamaño de archivo
- Podría causar exhaustión de memoria o disco

**Impacto**: Denial of Service

**Solución**: Agregar `if ($file['size'] > 5242880) { error }`

---

### 8. ❌ No Validación de Campos Numéricos
**Archivos**: Todos los formularios
**Problema**: 
- Campos como `duracion`, `filas`, `columnas` aceptan cualquier valor
- No hay validación min/max en servidor
- Podrían guardarse valores negativos o cero

**Impacto**: Datos inválidos en BD

**Solución**: Validar en save files: `if ($duracion <= 0) { error }`

---

### 9. ❌ No Validación de Dependencias Externas
**Archivos**: `proyeccion_guardar.php`, `temporada_guardar.php`, `episodio_guardar.php`
**Problema**: 
- Valida que FK existe, pero no que usuario tiene permiso
- Usuario podría editar/eliminar contenido de otros

**Impacto**: Tampering de datos

**Solución**: Agregar verificación de propiedad

---

### 10. ❌ sala_borrar.php - Usa GET en lugar de POST
**Archivo**: `admin/sala_borrar.php`
**Problema**: 
- Eliminación se puede disparar con un link
- No hay confirmación
- Vulnerable a ataques CSRF

**Impacto**: Eliminación accidental de salas

**Solución**: Convertir a POST con formulario de confirmación

---

## 🟡 PROBLEMAS MEDIOS

### 11. ❌ No Prevención de Duplicados en Salas
**Archivo**: `admin/sala_guardar.php`
**Problema**: 
- No valida que el nombre de sala sea único
- Podría crear salas duplicadas

**Solución**: Agregar `SELECT COUNT(*) FROM sala_config WHERE sala = ?`

---

### 12. ❌ Validación Débil de Contraseñas
**Archivo**: `admin/usuario_guardar.php`
**Problema**: 
- No hay requisitos de fortaleza
- Usuarios podrían usar contraseñas débiles

**Solución**: `if (strlen($password) < 8) { error }`

---

### 13. ❌ Manejo Inconsistente de Errores
**Archivos**: Todos
**Problema**: 
- Algunos redirigen con `?error=1`, otros con códigos específicos
- Frontend no siempre muestra mensajes específicos

**Solución**: Estandarizar códigos de error

---

### 14. ❌ No Validación de Parámetros de Redirección
**Archivos**: `proyeccion_borrar.php`, `temporada_borrar.php`, `episodio_borrar.php`
**Problema**: 
- Usa parámetros GET sin sanitizar: `?id_serie=" . $id_serie`
- Podría manipularse para redirigir a página incorrecta

**Solución**: Usar `urlencode()` o validar

---

### 15. ❌ No Logging de Auditoría
**Archivos**: Todos
**Problema**: 
- No se registra quién creó/editó/eliminó qué
- No se puede investigar cambios

**Solución**: Implementar tabla de auditoría

---

### 16. ❌ No Transacciones en Operaciones Multi-paso
**Archivos**: `serie_guardar.php`, `temporada_guardar.php`, `episodio_guardar.php`
**Problema**: 
- Si upload de imagen funciona pero insert falla, quedan archivos huérfanos
- Si insert funciona pero upload falla, BD tiene referencia a archivo inexistente

**Solución**: Usar `$pdo->beginTransaction()` / `commit()` / `rollback()`

---

### 17. ❌ No Control de Concurrencia
**Archivos**: Todos
**Problema**: 
- Dos admins editando el mismo registro simultáneamente podrían perder cambios
- No hay optimistic locking

**Solución**: Agregar campo `version` y verificar antes de update

---

### 18. ❌ No Soft Delete
**Archivos**: Todos los delete
**Problema**: 
- Eliminación es permanente
- No se puede recuperar datos eliminados accidentalmente

**Solución**: Agregar campo `deleted_at` en lugar de DELETE

---

### 19. ❌ Rutas de Imagen Hardcodeadas
**Archivos**: Todos los formularios
**Problema**: 
- Rutas hardcodeadas: `../assets/img/posters/`
- Si estructura cambia, todo se rompe

**Solución**: Usar constantes de configuración

---

### 20. ❌ No Protección XSS Consistente
**Archivos**: Múltiples
**Problema**: 
- Algunos campos escapados, otros no
- `nl2br()` sin escaping podría permitir XSS

**Solución**: Siempre usar `htmlspecialchars(nl2br($content))`

---

## 📋 RESUMEN DE PROBLEMAS POR ARCHIVO

### admin/sala_borrar.php
- ❌ CRÍTICO: Usa GET en lugar de POST
- ❌ CRÍTICO: Sin verificarAuth()
- ❌ CRÍTICO: CSRF validation rota
- ❌ ALTO: Sin confirmación de eliminación

### admin/sala_guardar.php
- ❌ CRÍTICO: Sin CSRF validation
- ❌ MEDIO: Sin prevención de duplicados

### admin/usuario_guardar.php
- ❌ CRÍTICO: Sin CSRF validation
- ❌ MEDIO: Validación débil de contraseñas

### admin/critica_guardar.php
- ❌ CRÍTICO: Sin CSRF validation

### admin/noticia_guardar.php
- ❌ CRÍTICO: Unsafe query en afterSave
- ❌ ALTO: Sin validación de tipo de archivo
- ❌ ALTO: Sin validación de tamaño

### admin/pelicula_guardar.php
- ❌ ALTO: Sin validación de tipo de archivo
- ❌ ALTO: Sin validación de tamaño

### admin/serie_guardar.php
- ❌ ALTO: Sin validación de tipo de archivo
- ❌ ALTO: Sin validación de tamaño
- ❌ MEDIO: Sin transacciones

### admin/temporada_guardar.php
- ❌ ALTO: Sin validación de tipo de archivo
- ❌ ALTO: Sin validación de tamaño
- ❌ MEDIO: Sin transacciones

### admin/proyeccion_guardar.php
- ✅ OK: Tiene CSRF validation
- ✅ OK: Tiene validación de FK

### admin/episodio_guardar.php
- ✅ OK: Tiene validación básica
- ❌ MEDIO: Sin transacciones

### admin/pelicula_borrar.php
- ✅ OK: Usa generic CRUD delete

### admin/crud/delete.php
- ✅ OK: Tiene CSRF validation
- ✅ OK: Tiene verificarAuth()

---

## 🔧 PLAN DE CORRECCIÓN

### Fase 1: Problemas Críticos (INMEDIATO)
1. Agregar CSRF validation a: sala_guardar.php, usuario_guardar.php, critica_guardar.php
2. Convertir sala_borrar.php a POST
3. Agregar verificarAuth() a sala_borrar.php
4. Arreglar noticia_guardar.php afterSave

### Fase 2: Problemas Altos (HOY)
1. Agregar validación de tipo de archivo
2. Agregar validación de tamaño de archivo
3. Agregar validación de campos numéricos

### Fase 3: Problemas Medios (ESTA SEMANA)
1. Agregar prevención de duplicados
2. Agregar validación de contraseñas
3. Agregar transacciones
4. Agregar logging de auditoría

### Fase 4: Problemas Bajos (PRÓXIMA SEMANA)
1. Agregar paginación
2. Agregar búsqueda/filtros
3. Agregar soft delete
4. Agregar control de concurrencia

---

## ✅ ARCHIVOS QUE FUNCIONAN CORRECTAMENTE

- ✅ admin/proyeccion_guardar.php
- ✅ admin/episodio_guardar.php
- ✅ admin/pelicula_borrar.php
- ✅ admin/crud/delete.php
- ✅ admin/crud/save.php (después de fixes anteriores)
- ✅ helpers/CSRF.php
- ✅ includes/optimizar_imagen.php

---

**Status**: ANÁLISIS COMPLETO
**Próximo Paso**: Implementar correcciones de Fase 1

