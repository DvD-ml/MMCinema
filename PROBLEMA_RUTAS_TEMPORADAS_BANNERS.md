# 🔴 PROBLEMA ENCONTRADO - RUTAS DE TEMPORADAS Y BANNERS

## ⚠️ PROBLEMA IDENTIFICADO

Los **posters de temporada** y **banners de series** no se ven en la página de detalle de serie porque hay un problema con cómo se guardan y se usan las rutas.

---

## 🔍 ANÁLISIS DEL PROBLEMA

### 1. **CÓMO SE GUARDAN LAS RUTAS**

En `admin/agregar_temporada.php` línea 16:
```php
$poster = mm_upload_image($_FILES['poster_file'] ?? [], 'assets/img/series/temporadas', 'temporada_poster');
```

La función `mm_upload_image` retorna:
```
assets/img/series/temporadas/temporada-poster_20260325_132938_9160f2cb.webp
```

**Se guarda en BD:** Ruta completa

---

### 2. **CÓMO SE USAN EN serie.php**

En `serie.php` línea 323:
```php
<img src="<?= htmlspecialchars($temporada['poster']) ?>" ...>
```

**Se usa directamente:** `assets/img/series/temporadas/temporada-poster_20260325_132938_9160f2cb.webp`

---

### 3. **EL PROBLEMA**

Cuando se accede desde la página de serie (`serie.php`), la ruta relativa es incorrecta porque:

- **Archivo:** `C:\xampp\htdocs\david\MMCINEMA\serie.php`
- **Ruta relativa guardada:** `assets/img/series/temporadas/...`
- **Ruta que busca:** `C:\xampp\htdocs\david\MMCINEMA\assets\img\series\temporadas\...` ✅ CORRECTO

**Pero en realidad:**
- Si la ruta en BD es `assets/img/series/temporadas/...`
- Y se usa directamente en `serie.php`
- Debería funcionar... 

**El verdadero problema es:**
Las rutas en la BD pueden estar incompletas o mal guardadas. Necesito verificar qué hay realmente en la BD.

---

## 🎯 SOLUCIÓN

### Opción 1: Verificar y Corregir Rutas en BD

Ejecutar en phpMyAdmin:

```sql
-- Ver qué rutas hay en temporadas
SELECT id, numero_temporada, poster FROM temporada LIMIT 10;

-- Ver qué rutas hay en series (banners)
SELECT id, titulo, banner FROM serie LIMIT 10;
```

### Opción 2: Corregir en serie.php

Cambiar línea 323 de:
```php
<img src="<?= htmlspecialchars($temporada['poster']) ?>" ...>
```

A:
```php
<img src="<?= !empty($temporada['poster']) ? htmlspecialchars($temporada['poster']) : 'assets/img/series/temporadas/placeholder.webp' ?>" ...>
```

### Opción 3: Crear Helper para Rutas

Crear función en `config/conexion.php`:

```php
function getImagePath($type, $path) {
    if (empty($path)) {
        return 'assets/img/placeholder.webp';
    }
    
    // Si ya tiene ruta completa, devolverla
    if (strpos($path, 'assets/') === 0) {
        return $path;
    }
    
    // Si no, construir ruta según tipo
    switch ($type) {
        case 'temporada':
            return 'assets/img/series/temporadas/' . $path;
        case 'banner':
            return 'assets/img/series/banners/' . $path;
        case 'poster_serie':
            return 'assets/img/series/posters/' . $path;
        default:
            return $path;
    }
}
```

Luego usar en `serie.php`:

```php
<img src="<?= getImagePath('temporada', $temporada['poster']) ?>" ...>
```

---

## 📋 CHECKLIST DE VERIFICACIÓN

- [ ] Verificar qué rutas hay en BD para temporadas
- [ ] Verificar qué rutas hay en BD para banners de series
- [ ] Comparar con las rutas que existen en el sistema de archivos
- [ ] Corregir rutas en BD si es necesario
- [ ] Probar que se muestren correctamente

---

## 🚀 PRÓXIMOS PASOS

1. **Verificar BD** - Ver qué rutas hay realmente guardadas
2. **Comparar** - Comparar con archivos que existen
3. **Corregir** - Actualizar rutas en BD si es necesario
4. **Probar** - Verificar que se muestren en navegador

---

**¿Quieres que cree un script para verificar y corregir las rutas?** 🔧
