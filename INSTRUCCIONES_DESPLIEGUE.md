# INSTRUCCIONES DE DESPLIEGUE - MMCINEMA

**Fecha:** 28 de Abril de 2026

---

## PRE-DESPLIEGUE

### 1. Verificación de Archivos
```bash
# Verificar que no hay conflictos de merge
git status

# Verificar que no hay cambios sin guardar
git diff

# Ver el log de commits
git log --oneline -10
```

### 2. Verificación de Base de Datos
```bash
# Conectar a MySQL
mysql -u root -p mmcinema3

# Verificar tablas
SHOW TABLES;

# Verificar estructura de carrusel_destacado
DESCRIBE carrusel_destacado;

# Verificar datos
SELECT COUNT(*) FROM carrusel_destacado WHERE activo = 1;
```

### 3. Verificación de Archivos
```bash
# Verificar que existen las imágenes del carrusel
ls -la assets/img/carrusel/

# Verificar que existen los logos
ls -la assets/img/logos/

# Verificar que existen los posters
ls -la assets/img/posters/
```

### 4. Verificación de Configuración
```bash
# Verificar que .env existe
cat .env

# Verificar que tiene las variables correctas
grep MAIL_ .env
grep DB_ .env
grep BASE_URL .env
```

---

## DESPLIEGUE EN STAGING

### 1. Clonar Repositorio
```bash
git clone <repo-url> mmcinema-staging
cd mmcinema-staging
```

### 2. Instalar Dependencias
```bash
# Instalar Composer
composer install

# Instalar npm (si es necesario)
npm install
```

### 3. Configurar Entorno
```bash
# Copiar .env.example a .env
cp .env.example .env

# Editar .env con valores de staging
nano .env
```

### 4. Crear Base de Datos
```bash
# Crear BD
mysql -u root -p -e "CREATE DATABASE mmcinema3_staging;"

# Importar schema
mysql -u root -p mmcinema3_staging < database/mmcinema3.sql
```

### 5. Verificar Permisos
```bash
# Dar permisos a carpetas
chmod -R 755 assets/
chmod -R 755 admin/logo/
chmod -R 755 helpers/

# Dar permisos a archivos
chmod 644 .env
```

### 6. Pruebas Básicas
```bash
# Verificar que la página carga
curl http://localhost/mmcinema-staging/

# Verificar que el carrusel carga
curl http://localhost/mmcinema-staging/ | grep "netflix-carousel"

# Verificar que no hay errores
tail -f /var/log/apache2/error.log
```

---

## DESPLIEGUE EN PRODUCCIÓN

### 1. Backup de Producción
```bash
# Backup de BD
mysqldump -u root -p mmcinema3 > backup_mmcinema3_$(date +%Y%m%d).sql

# Backup de archivos
tar -czf backup_mmcinema3_$(date +%Y%m%d).tar.gz /var/www/mmcinema/
```

### 2. Actualizar Código
```bash
cd /var/www/mmcinema/

# Hacer pull del repositorio
git pull origin main

# Instalar dependencias
composer install --no-dev
```

### 3. Actualizar Base de Datos
```bash
# Ejecutar migraciones (si las hay)
php artisan migrate --force

# O importar schema si es necesario
mysql -u root -p mmcinema3 < database/mmcinema3.sql
```

### 4. Limpiar Caché
```bash
# Limpiar caché de aplicación
rm -rf storage/cache/*

# Limpiar caché de navegador (si es necesario)
# Esto se hace desde el navegador del usuario
```

### 5. Verificar Funcionamiento
```bash
# Verificar que la página carga
curl https://mmcinema.com/

# Verificar que el carrusel carga
curl https://mmcinema.com/ | grep "netflix-carousel"

# Verificar que no hay errores
tail -f /var/log/apache2/error.log
```

---

## ROLLBACK (Si algo sale mal)

### 1. Revertir Código
```bash
# Revertir al commit anterior
git revert HEAD

# O hacer reset (cuidado, esto borra cambios)
git reset --hard HEAD~1
```

### 2. Restaurar Base de Datos
```bash
# Restaurar desde backup
mysql -u root -p mmcinema3 < backup_mmcinema3_YYYYMMDD.sql
```

### 3. Restaurar Archivos
```bash
# Restaurar desde backup
tar -xzf backup_mmcinema3_YYYYMMDD.tar.gz -C /var/www/
```

---

## VERIFICACIÓN POST-DESPLIEGUE

### 1. Verificar Página de Inicio
- [ ] Carrusel visible
- [ ] Imágenes cargando
- [ ] Caracteres especiales correctos
- [ ] Sin errores en consola

### 2. Verificar Autenticación
- [ ] Login funciona
- [ ] Rate limiting funciona
- [ ] Sesión se mantiene

### 3. Verificar Admin
- [ ] Acceso requiere autenticación
- [ ] Formularios tienen CSRF
- [ ] Carga de archivos funciona

### 4. Verificar Seguridad
- [ ] No hay credenciales expuestas
- [ ] CSRF tokens presentes
- [ ] Validación MIME funciona

### 5. Verificar Rendimiento
- [ ] Página carga rápido
- [ ] No hay consultas lentas
- [ ] Caché funciona

---

## MONITOREO POST-DESPLIEGUE

### 1. Logs
```bash
# Ver logs de error
tail -f /var/log/apache2/error.log

# Ver logs de acceso
tail -f /var/log/apache2/access.log

# Ver logs de PHP
tail -f /var/log/php-fpm.log
```

### 2. Métricas
- Tiempo de respuesta
- Uso de CPU
- Uso de memoria
- Conexiones a BD

### 3. Alertas
- Errores 500
- Errores 404
- Errores de BD
- Intentos de ataque

---

## CHECKLIST FINAL

### Antes de Desplegar
- [ ] Todos los tests pasan
- [ ] No hay conflictos de merge
- [ ] Backup de BD realizado
- [ ] Backup de archivos realizado
- [ ] .env configurado correctamente
- [ ] Permisos de archivos correctos

### Durante el Despliegue
- [ ] Código actualizado
- [ ] Dependencias instaladas
- [ ] BD actualizada
- [ ] Caché limpiado
- [ ] Servicios reiniciados

### Después del Despliegue
- [ ] Página carga correctamente
- [ ] Carrusel visible
- [ ] Login funciona
- [ ] Admin funciona
- [ ] No hay errores
- [ ] Rendimiento aceptable

---

## CONTACTO DE EMERGENCIA

Si algo sale mal durante el despliegue:

1. **Rollback inmediato** - Revertir cambios
2. **Restaurar BD** - Desde backup
3. **Restaurar archivos** - Desde backup
4. **Contactar equipo** - Para investigar

---

## NOTAS IMPORTANTES

1. **Siempre hacer backup** antes de desplegar
2. **Probar en staging** antes de producción
3. **Monitorear logs** después de desplegar
4. **Tener plan de rollback** listo
5. **Comunicar cambios** al equipo

---

**Última actualización:** 28 de Abril de 2026
