# 📤 INSTRUCCIONES DE DEPLOY - FASE 1

## Status: LISTO PARA SUBIR AL SERVIDOR ✅

**Fecha**: May 4, 2026
**Archivos a Subir**: 6
**Cambios**: Críticos + Seguridad
**Tiempo de Deploy**: 5 minutos

---

## 📋 ARCHIVOS A SUBIR

### Archivos Modificados (6 total)
```
admin/sala_borrar.php
admin/salas.php
admin/sala_guardar.php
admin/usuario_guardar.php
admin/critica_guardar.php
admin/noticia_guardar.php
```

---

## 🚀 COMANDO DE DEPLOY

### Opción 1: Subir toda la carpeta admin (RECOMENDADO)
```bash
scp -r admin/ root@200.234.233.50:/var/www/html/mmcinema/
```

### Opción 2: Subir solo archivos modificados
```bash
scp admin/sala_borrar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/salas.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/sala_guardar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/usuario_guardar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/critica_guardar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
scp admin/noticia_guardar.php root@200.234.233.50:/var/www/html/mmcinema/admin/
```

---

## ✅ PASOS POST-DEPLOY

### 1. Verificar que los archivos se subieron correctamente
```bash
ssh root@200.234.233.50
ls -la /var/www/html/mmcinema/admin/sala_borrar.php
ls -la /var/www/html/mmcinema/admin/salas.php
# ... etc
```

### 2. Verificar permisos de archivos
```bash
chmod 644 /var/www/html/mmcinema/admin/*.php
```

### 3. Limpiar cache del navegador
- **Windows**: Ctrl+Shift+Supr
- **Mac**: Cmd+Shift+Delete
- **Linux**: Ctrl+Shift+Delete

### 4. Probar en navegador
- Ir a: `http://200.234.233.50/mmcinema/admin/salas.php`
- Verificar que carga correctamente

---

## 🧪 PRUEBAS A REALIZAR

### Test 1: Crear Sala
1. Ir a Administrar Salas
2. Click en "Añadir sala"
3. Llenar formulario:
   - Nombre: "Sala Test"
   - Filas: 10
   - Columnas: 15
4. Click en "Crear sala"
5. ✅ Debe redirigir a lista con mensaje de éxito

### Test 2: Editar Sala
1. Click en "Editar" en una sala
2. Cambiar número de filas
3. Click en "Guardar cambios"
4. ✅ Debe redirigir a lista con mensaje de éxito

### Test 3: Eliminar Sala (IMPORTANTE)
1. Click en "Eliminar" en una sala
2. Debe aparecer confirmación: "¿Eliminar esta sala?"
3. Click en "Aceptar"
4. ✅ Debe redirigir a lista con mensaje de éxito
5. ✅ Sala debe estar eliminada

### Test 4: Prevención de Duplicados
1. Crear sala con nombre "Sala Duplicada"
2. Intentar crear otra sala con mismo nombre
3. ✅ Debe mostrar error "duplicado"

### Test 5: Validación de Campos
1. Intentar crear sala con filas = 0
2. ✅ Debe mostrar error
3. Intentar crear sala con columnas = -5
4. ✅ Debe mostrar error

### Test 6: Crear Usuario
1. Ir a Administrar Usuarios
2. Click en "Añadir usuario"
3. Llenar formulario:
   - Username: "testuser"
   - Email: "test@example.com"
   - Contraseña: "test1234"
   - Rol: "usuario"
4. Click en "Crear usuario"
5. ✅ Debe redirigir a lista con mensaje de éxito

### Test 7: Validación de Contraseña
1. Intentar crear usuario con contraseña "123"
2. ✅ Debe mostrar error "password_weak"
3. Intentar crear usuario sin contraseña
4. ✅ Debe mostrar error "password"

### Test 8: Crear Crítica
1. Ir a Administrar Críticas
2. Click en "Añadir crítica"
3. Llenar formulario
4. Click en "Crear crítica"
5. ✅ Debe redirigir a lista con mensaje de éxito

### Test 9: Crear Noticia
1. Ir a Administrar Noticias
2. Click en "Añadir noticia"
3. Llenar formulario
4. Click en "Crear noticia"
5. ✅ Debe redirigir a lista con mensaje de éxito
6. ✅ Fecha de publicación debe estar establecida

### Test 10: CSRF Protection
1. Abrir consola del navegador (F12)
2. Ir a formulario de sala
3. Inspeccionar elemento del formulario
4. ✅ Debe haber campo `csrf_token` oculto
5. Intentar enviar formulario sin token
6. ✅ Debe fallar con error 403

---

## 🔍 VERIFICACIÓN DE SEGURIDAD

### Verificar CSRF Tokens
```bash
# SSH al servidor
ssh root@200.234.233.50

# Buscar CSRF validation en archivos
grep -r "CSRF::validarOAbortar" /var/www/html/mmcinema/admin/

# Debe mostrar:
# admin/sala_borrar.php
# admin/sala_guardar.php
# admin/usuario_guardar.php
# admin/critica_guardar.php
# admin/noticia_guardar.php
# admin/pelicula_guardar.php
# admin/proyeccion_guardar.php
# admin/serie_guardar.php
# admin/temporada_guardar.php
# admin/episodio_guardar.php
```

### Verificar Autenticación
```bash
# Buscar verificarAuth en archivos delete
grep -r "verificarAuth" /var/www/html/mmcinema/admin/*_borrar.php

# Debe mostrar:
# admin/sala_borrar.php
# admin/pelicula_borrar.php
# admin/noticia_borrar.php
# admin/proyeccion_borrar.php
# admin/serie_borrar.php
# admin/temporada_borrar.php
# admin/episodio_borrar.php
# admin/usuario_borrar.php
# admin/critica_borrar.php
```

---

## ⚠️ POSIBLES PROBLEMAS Y SOLUCIONES

### Problema 1: "Token CSRF inválido"
**Causa**: Sesión expirada o token no generado
**Solución**: 
1. Limpiar cache del navegador
2. Cerrar sesión y volver a iniciar
3. Verificar que `helpers/CSRF.php` está en servidor

### Problema 2: "Error al procesar la sala"
**Causa**: Validación fallida
**Solución**:
1. Verificar que todos los campos están llenos
2. Verificar que filas y columnas son > 0
3. Verificar que no hay duplicados

### Problema 3: "Sala duplicada"
**Causa**: Ya existe una sala con ese nombre
**Solución**:
1. Usar nombre diferente
2. O editar la sala existente

### Problema 4: Eliminación no funciona
**Causa**: Formulario POST no se envía
**Solución**:
1. Verificar que JavaScript está habilitado
2. Verificar que formulario tiene CSRF token
3. Verificar que método es POST

### Problema 5: Contraseña débil no se rechaza
**Causa**: Validación no se ejecutó
**Solución**:
1. Verificar que archivo `usuario_guardar.php` se subió
2. Verificar que tiene validación de longitud
3. Limpiar cache del navegador

---

## 📊 CHECKLIST DE DEPLOY

### Pre-Deploy
- [ ] Todos los archivos están listos
- [ ] Cambios han sido probados localmente
- [ ] Backup de archivos originales hecho
- [ ] Conexión SSH verificada

### Deploy
- [ ] Archivos subidos correctamente
- [ ] Permisos de archivos verificados
- [ ] Cache del navegador limpiado

### Post-Deploy
- [ ] Página carga correctamente
- [ ] Test 1: Crear sala ✅
- [ ] Test 2: Editar sala ✅
- [ ] Test 3: Eliminar sala ✅
- [ ] Test 4: Prevención de duplicados ✅
- [ ] Test 5: Validación de campos ✅
- [ ] Test 6: Crear usuario ✅
- [ ] Test 7: Validación de contraseña ✅
- [ ] Test 8: Crear crítica ✅
- [ ] Test 9: Crear noticia ✅
- [ ] Test 10: CSRF protection ✅

### Verificación de Seguridad
- [ ] CSRF tokens presentes en todos los forms
- [ ] Autenticación verificada en delete files
- [ ] POST-based deletion funcionando
- [ ] Validación de campos funcionando

---

## 🔄 ROLLBACK PLAN

Si algo sale mal, revertir cambios:

### Opción 1: Restaurar desde backup
```bash
ssh root@200.234.233.50
cp -r /backup/admin /var/www/html/mmcinema/
```

### Opción 2: Revertir archivos individuales
```bash
ssh root@200.234.233.50
git checkout admin/sala_borrar.php
git checkout admin/salas.php
# ... etc
```

### Opción 3: Restaurar desde local
```bash
# En tu máquina local
git checkout admin/
scp -r admin/ root@200.234.233.50:/var/www/html/mmcinema/
```

---

## 📞 SOPORTE

Si hay problemas:

1. **Verificar logs del servidor**
   ```bash
   ssh root@200.234.233.50
   tail -f /var/log/php-fpm/error.log
   ```

2. **Verificar permisos**
   ```bash
   ls -la /var/www/html/mmcinema/admin/
   ```

3. **Verificar que archivos se subieron**
   ```bash
   grep "CSRF::validarOAbortar" /var/www/html/mmcinema/admin/sala_guardar.php
   ```

4. **Limpiar cache de PHP**
   ```bash
   ssh root@200.234.233.50
   systemctl restart php-fpm
   ```

---

## ✅ CONFIRMACIÓN

Una vez completado el deploy:

1. ✅ Todos los archivos subidos
2. ✅ Todos los tests pasados
3. ✅ Seguridad verificada
4. ✅ Sistema funcionando correctamente

**Status**: LISTO PARA DEPLOY
**Próximo**: Fase 2 - Validación de archivos

