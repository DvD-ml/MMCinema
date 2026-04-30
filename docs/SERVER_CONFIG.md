# 🖥️ Configuración del Servidor - MMCinema

Tareas de configuración pendientes en el servidor de producción.

## Estado Actual

**Servidor**: 200.234.233.50
**OS**: Ubuntu 22.04 LTS
**Estado**: ✅ Funcional (Falta configuración de seguridad)

## ✅ Ya Configurado

- ✅ Apache instalado y funcionando
- ✅ PHP 8.1 instalado
- ✅ MySQL instalado
- ✅ Base de datos `mmcinema_prod` creada
- ✅ Proyecto MMCinema desplegado
- ✅ CSRF tokens implementados
- ✅ Carpeta `/tickets` con permisos correctos
- ✅ HTTPS configurado (certificado autofirmado)

## 🔴 CRÍTICO - Hacer Ahora

### 1. Comprar Dominio Personalizado

**Por qué**: El sitio actualmente es accesible solo por IP

**Opciones**:
- Namecheap: $5-10/año
- GoDaddy: $10-15/año
- Hostinger: $3-5/año

**Pasos**:
1. Compra el dominio
2. Apunta DNS a `200.234.233.50`
3. Espera 24 horas a que se propague
4. Ejecuta: `sudo certbot --apache -d tudominio.com`

**Tiempo**: 30 minutos
**Costo**: $5-15/año

### 2. Obtener Certificado SSL Válido

**Por qué**: El certificado actual es autofirmado (muestra advertencia)

**Pasos**:
1. Compra un dominio (ver arriba)
2. Ejecuta: `sudo certbot --apache -d tudominio.com -d www.tudominio.com`
3. Certbot renovará automáticamente

**Tiempo**: 5 minutos
**Costo**: Gratis (Let's Encrypt)

## 🟡 IMPORTANTE - Esta Semana

### 3. Configurar Firewall

**Por qué**: Proteger el servidor de accesos no autorizados

```bash
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw status
```

**Tiempo**: 5 minutos
**Costo**: Gratis

### 4. Configurar Backups Automáticos

**Por qué**: Proteger datos en caso de fallo

**Script**:
```bash
#!/bin/bash
BACKUP_DIR="/backups/mmcinema"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR
mysqldump -u root -pmmcinema_root_2026 mmcinema_prod > $BACKUP_DIR/db_$DATE.sql
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/mmcinema
find $BACKUP_DIR -type f -mtime +30 -delete
echo "Backup completado: $DATE"
```

**Configurar cron**:
```bash
sudo crontab -e
# Agregar: 0 2 * * * /root/backup_mmcinema.sh
```

**Tiempo**: 15 minutos
**Costo**: Gratis

### 5. Habilitar Emails (Opcional)

**Por qué**: Enviar notificaciones a usuarios

**Nota**: El servidor actual bloquea SMTP saliente

**Opciones**:
1. Cambiar a hosting que permita SMTP
2. Usar SendGrid API (requiere cambio de hosting)
3. Instalar servidor de email local (complejo)

**Tiempo**: 1-2 horas
**Costo**: Gratis o $20-50/mes

## 🟢 RECOMENDADO - Este Mes

### 6. Optimizar Rendimiento

```bash
# Habilitar caché PHP
sudo apt-get install php8.1-opcache

# Habilitar compresión Gzip
sudo a2enmod deflate
sudo systemctl restart apache2
```

**Tiempo**: 20 minutos
**Costo**: Gratis

### 7. Configurar Monitoreo

**Opciones**:
- UptimeRobot (gratis)
- Pingdom
- StatusCake

**Tiempo**: 10 minutos
**Costo**: Gratis

### 8. Configurar Logs

```bash
# Ver logs en tiempo real
tail -f /var/log/apache2/mmcinema_error.log

# Configurar rotación de logs
sudo nano /etc/logrotate.d/mmcinema
```

**Tiempo**: 10 minutos
**Costo**: Gratis

## 📊 Resumen

| Tarea | Prioridad | Tiempo | Costo | Estado |
|-------|-----------|--------|-------|--------|
| Dominio | 🔴 CRÍTICA | 30 min | $5-15/año | ❌ |
| SSL Válido | 🔴 CRÍTICA | 5 min | Gratis | ⚠️ |
| Firewall | 🟡 IMPORTANTE | 5 min | Gratis | ❌ |
| Backups | 🟡 IMPORTANTE | 15 min | Gratis | ❌ |
| Emails | 🟡 IMPORTANTE | 1-2h | Gratis | ❌ |
| Rendimiento | 🟢 RECOMENDADO | 20 min | Gratis | ⚠️ |
| Monitoreo | 🟢 RECOMENDADO | 10 min | Gratis | ❌ |
| Logs | 🟢 RECOMENDADO | 10 min | Gratis | ⚠️ |

## 📞 Contacto

Para ayuda con la configuración del servidor, contacta a Clouding o a un administrador de sistemas.

---

**Última actualización**: 30 de Abril de 2026
**Servidor**: 200.234.233.50
**Estado**: 🟢 FUNCIONAL
