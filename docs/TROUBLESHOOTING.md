# 🔧 Solución de Problemas - MMCinema

Guía para resolver problemas comunes en MMCinema.

## Problemas Comunes

### 1. Error de Conexión a Base de Datos

**Síntoma**: "Error connecting to database"

**Soluciones**:
1. Verifica que MySQL está corriendo: `sudo systemctl status mysql`
2. Verifica credenciales en `.env`
3. Verifica que la BD existe: `mysql -u root -p -e "SHOW DATABASES;"`
4. Reinicia MySQL: `sudo systemctl restart mysql`

### 2. Página en Blanco

**Síntoma**: Página completamente en blanco

**Soluciones**:
1. Revisa logs de PHP: `tail -f /var/log/apache2/mmcinema_error.log`
2. Revisa logs de Apache: `tail -f /var/log/apache2/error.log`
3. Verifica permisos de carpetas: `chmod 755 /var/www/html/mmcinema`
4. Verifica que index.php existe

### 3. Error 404 - Página No Encontrada

**Síntoma**: "404 Not Found"

**Soluciones**:
1. Verifica que `.htaccess` está en la raíz
2. Verifica que mod_rewrite está habilitado: `sudo a2enmod rewrite`
3. Reinicia Apache: `sudo systemctl restart apache2`

### 4. Error de Permisos

**Síntoma**: "Permission denied"

**Soluciones**:
1. Carpeta de tickets: `sudo chmod 777 /var/www/html/mmcinema/storage/tickets`
2. Carpeta de logs: `sudo chmod 755 /var/www/html/mmcinema/logs`
3. Archivos: `sudo chown -R www-data:www-data /var/www/html/mmcinema`

### 5. Emails No Se Envían

**Síntoma**: Los usuarios no reciben emails

**Soluciones**:
1. Verifica que SMTP está habilitado en el servidor
2. Verifica credenciales en `config/mail.php`
3. Revisa logs: `tail -f /var/log/mail.log`
4. Nota: El servidor actual bloquea SMTP saliente

### 6. SSL/HTTPS No Funciona

**Síntoma**: "Not secure" o error de certificado

**Soluciones**:
1. Verifica que mod_ssl está habilitado: `sudo a2enmod ssl`
2. Verifica que el certificado existe: `ls -la /etc/ssl/certs/mmcinema.crt`
3. Reinicia Apache: `sudo systemctl restart apache2`

### 7. Tickets PDF No Se Generan

**Síntoma**: Error al generar PDF de ticket

**Soluciones**:
1. Verifica que FPDF está instalado: `composer install`
2. Verifica permisos de carpeta: `chmod 777 /var/www/html/mmcinema/storage/tickets`
3. Revisa logs: `tail -f /var/log/apache2/mmcinema_error.log`

### 8. Admin Panel No Carga

**Síntoma**: Error al acceder a `/admin`

**Soluciones**:
1. Verifica que estás logueado
2. Verifica que tienes permisos de admin
3. Revisa logs: `tail -f /var/log/apache2/mmcinema_error.log`

## Logs Útiles

### Apache
```bash
# Error log
tail -f /var/log/apache2/mmcinema_error.log

# Access log
tail -f /var/log/apache2/mmcinema_access.log
```

### MySQL
```bash
# Error log
tail -f /var/log/mysql/error.log
```

### PHP
```bash
# Log de aplicación
tail -f /var/www/html/mmcinema/logs/app.log
```

## Comandos Útiles

### Reiniciar Servicios
```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
```

### Ver Estado
```bash
sudo systemctl status apache2
sudo systemctl status mysql
```

### Verificar Conectividad
```bash
# Conectar a BD
mysql -u mmcinema_user -pmmcinema_pass_2026 mmcinema_prod

# Verificar puerto 80
sudo netstat -tlnp | grep :80

# Verificar puerto 443
sudo netstat -tlnp | grep :443
```

## Contacto

Si el problema persiste, contacta al equipo de desarrollo.

---

**Última actualización**: 30 de Abril de 2026
