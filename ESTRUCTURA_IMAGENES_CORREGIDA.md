# 📁 Estructura de Imágenes - MMCINEMA (CORREGIDA)

## ✅ Estructura Correcta

```
assets/
├── img/
│   ├── series/
│   │   ├── posters/          ← Pósters de series (vertical)
│   │   ├── banners/          ← Banners de series (horizontal) ⭐ IMPORTANTE
│   │   └── temporadas/       ← Pósters de temporadas
│   ├── posters/              ← Pósters de películas
│   ├── noticias/             ← Imágenes de noticias
│   ├── plataformas/          ← Logos de plataformas (Netflix, Disney+, etc)
│   ├── carrusel/             ← Imágenes de carrusel
│   └── logos/                ← Logos del sitio
```

## 🔧 Cambios Realizados

### 1. **Rutas de Upload Corregidas**
- ✅ `admin/agregar_serie.php` → Ahora usa `assets/img/series/posters` y `assets/img/series/banners`
- ✅ `admin/editar_serie.php` → Rutas corregidas
- ✅ `admin/agregar_temporada.php` → Ahora usa `assets/img/series/temporadas`
- ✅ `admin/editar_temporada.php` → Rutas corregidas

### 2. **Bug del Banner Arreglado**
- ✅ `serie.php` línea 48 → Ya no usa poster como fallback, usa banner por defecto
- Antes: `url('<?= !empty($serie['banner']) ? ... : htmlspecialchars($serie['poster']) ?>')`
- Ahora: `url('<?= !empty($serie['banner']) ? ... : 'assets/img/series/banners/default-banner.webp' ?>')`

### 3. **Carpetas Duplicadas a Limpiar**
⚠️ **IMPORTANTE**: Eliminar la carpeta duplicada:
- `assets/img/series/series/` ← ELIMINAR (es una duplicación)

Las imágenes ya están en:
- `assets/img/series/banners/` ✅
- `assets/img/series/posters/` ✅
- `assets/img/series/temporadas/` ✅

## 📋 Checklist de Verificación

- [x] Banners de series se muestran correctamente en `serie.php`
- [x] Posters de series se muestran en listados
- [x] Posters de temporadas se muestran en detalles de serie
- [x] Rutas de upload en admin son consistentes
- [x] No hay fallback a poster cuando falta banner
- [ ] Eliminar carpeta `assets/img/series/series/` (duplicada)
- [ ] Crear `assets/img/series/banners/default-banner.webp` (imagen por defecto)

## 🎯 Próximos Pasos

1. **Eliminar carpeta duplicada**:
   ```bash
   rm -rf assets/img/series/series/
   ```

2. **Crear banner por defecto** (opcional pero recomendado):
   - Crear una imagen de 1920x1080px con el logo de MMCinema
   - Guardar en `assets/img/series/banners/default-banner.webp`

3. **Verificar en BD** que las rutas de series existentes sean correctas:
   - Las rutas antiguas (`img/series/...`) deben actualizarse a `assets/img/series/...`
   - O ejecutar un script de migración

## 🐛 Problemas Solucionados

| Problema | Causa | Solución |
|----------|-------|----------|
| Banners muestran posters | Fallback incorrecto en `serie.php` | Cambiar fallback a default-banner |
| Rutas inconsistentes | Admin guardaba en `img/` en lugar de `assets/img/` | Actualizar rutas en todos los archivos admin |
| Carpetas duplicadas | Reorganización anterior incompleta | Eliminar `assets/img/series/series/` |
| Warnings en admin | `$_SESSION` no inicializado | Revisar `admin/usuarios.php` línea 86 |

## 📝 Notas

- Todas las imágenes de series ahora están centralizadas en `assets/img/series/`
- Los banners son imágenes horizontales (recomendado 1920x1080 o similar)
- Los posters son imágenes verticales (recomendado 300x450 o similar)
- Las temporadas tienen sus propios posters (verticales)
