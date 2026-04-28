# ✅ CHECKLIST DE EJECUCIÓN

## 🎯 ANTES DE COMENZAR

- [ ] Tienes acceso a `http://localhost/david/MMCINEMA/`
- [ ] Tienes PowerShell disponible
- [ ] Tienes acceso a `C:\xampp\htdocs\david\MMCINEMA\`
- [ ] Has leído `COMIENZA_AQUI_AHORA.md`

---

## 🚀 PASO 1: CORREGIR RUTAS EN BD (5 minutos)

### Ejecución
- [ ] Abre en navegador: `http://localhost/david/MMCINEMA/corregir_rutas_bd.php`
- [ ] Espera a que cargue completamente
- [ ] Verifica que veas el mensaje de corrección

### Verificación
- [ ] ✅ 42 registros actualizados (posters de temporadas)
- [ ] ✅ 15 registros actualizados (posters de series)
- [ ] ✅ 19 registros actualizados (banners de series)

### Confirmación
- [ ] Abre: `http://localhost/david/MMCINEMA/verificar_rutas.php`
- [ ] Verifica que todos muestren ✅ OK
- [ ] Si hay ❌ NO EXISTE, algo salió mal (contacta)

---

## 🧹 PASO 2: LIMPIAR CARPETAS DUPLICADAS (2 minutos)

### Preparación
- [ ] Abre PowerShell
- [ ] Navega a: `C:\xampp\htdocs\david\MMCINEMA`
- [ ] Ejecuta: `cd C:\xampp\htdocs\david\MMCINEMA`

### Ejecución - Comando 1
- [ ] Ejecuta: `Remove-Item -Recurse -Force "assets\img\noticias\noticias"`
- [ ] Verifica que se elimine sin errores
- [ ] Deberías ver: `(sin salida = éxito)`

### Ejecución - Comando 2
- [ ] Ejecuta: `Remove-Item -Recurse -Force "assets\img\series\series"`
- [ ] Verifica que se elimine sin errores
- [ ] Deberías ver: `(sin salida = éxito)`

### Ejecución - Comando 3
- [ ] Ejecuta: `Remove-Item -Recurse -Force "img"`
- [ ] Verifica que se elimine sin errores
- [ ] Deberías ver: `(sin salida = éxito)`

### Verificación
- [ ] Abre el Explorador de archivos
- [ ] Navega a: `C:\xampp\htdocs\david\MMCINEMA\assets\img\`
- [ ] Verifica que NO exista: `noticias\noticias\`
- [ ] Verifica que NO exista: `series\series\`
- [ ] Navega a: `C:\xampp\htdocs\david\MMCINEMA\`
- [ ] Verifica que NO exista: `img\`

---

## 🌐 PASO 3: VERIFICAR EN NAVEGADOR (2 minutos)

### Verificación General
- [ ] Abre: `http://localhost/david/MMCINEMA/`
- [ ] Verifica que cargue sin errores
- [ ] Verifica que se vean las imágenes de noticias
- [ ] Verifica que se vean los posters de películas

### Verificación de Series
- [ ] Abre: `http://localhost/david/MMCINEMA/series.php`
- [ ] Verifica que se vean los posters de series
- [ ] Verifica que se vean los banners de series

### Verificación Detallada
- [ ] Abre: `http://localhost/david/MMCINEMA/serie.php?id=1`
- [ ] Verifica que se vea el banner (fondo)
- [ ] Verifica que se vean los posters de temporadas
- [ ] Verifica que se vean los posters de series

### Verificación de Errores
- [ ] Abre la consola: Presiona F12
- [ ] Verifica que NO haya errores 404
- [ ] Verifica que NO haya errores de red
- [ ] Cierra la consola: Presiona F12 nuevamente

---

## 📊 VERIFICACIÓN FINAL

### Rutas en BD
- [ ] Ejecuta: `http://localhost/david/MMCINEMA/verificar_rutas.php`
- [ ] Verifica que todos muestren ✅ OK
- [ ] Verifica que NO haya ❌ NO EXISTE

### Estructura de Carpetas
- [ ] Verifica que `assets/img/noticias/noticias/` NO exista
- [ ] Verifica que `assets/img/series/series/` NO exista
- [ ] Verifica que `img/` NO exista

### Imágenes Visibles
- [ ] Posters de temporadas: ✅ SE VEN
- [ ] Banners de series: ✅ SE VEN
- [ ] Posters de series: ✅ SE VEN
- [ ] Imágenes de noticias: ✅ SE VEN

---

## 🎉 RESULTADO FINAL

### Antes
```
❌ Posters de temporadas: NO SE VEN
❌ Banners de series: NO SE VEN
❌ 43 archivos duplicados
❌ 50-100 MB desperdiciados
```

### Después
```
✅ Posters de temporadas: SE VEN
✅ Banners de series: SE VEN
✅ Sin duplicaciones
✅ Espacio liberado
```

---

## 📝 NOTAS

- [ ] Anota cualquier error que veas
- [ ] Anota cualquier problema que encuentres
- [ ] Guarda este checklist para referencia futura

---

## 🆘 SI ALGO SALE MAL

### Si las imágenes aún no se ven:
1. Ejecuta `verificar_rutas.php` nuevamente
2. Verifica que todas las rutas muestren ✅ OK
3. Si hay ❌ NO EXISTE, contacta

### Si los comandos PowerShell fallan:
1. Verifica que estés en la carpeta correcta
2. Verifica que tengas permisos de administrador
3. Intenta nuevamente

### Si algo se rompe:
1. Restaura desde Git: `git checkout -- .`
2. O restaura desde backup si tienes

---

## ✅ CHECKLIST COMPLETADO

- [ ] Paso 1: Rutas en BD corregidas
- [ ] Paso 2: Carpetas duplicadas eliminadas
- [ ] Paso 3: Verificación en navegador completada
- [ ] Verificación final completada
- [ ] ¡PROYECTO FUNCIONAL!

---

## 🎯 PRÓXIMOS PASOS (Opcional)

Después de completar esto, puedes:
- [ ] Hacer backup del proyecto
- [ ] Hacer commit en Git
- [ ] Documentar los cambios
- [ ] Informar al equipo

---

**¡FELICIDADES! Tu proyecto está limpio y funcional.** 🎉

