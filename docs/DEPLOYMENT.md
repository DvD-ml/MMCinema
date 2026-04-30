# 🚀 Deployment - MMCinema

Guía para desplegar MMCinema a un servidor de producción.

## Servidor Actual

- **IP**: 200.234.233.50
- **OS**: Ubuntu 22.04 LTS
- **Servidor Web**: Apache 2.4
- **PHP**: 8.1
- **Base de Datos**: MySQL

## Requisitos

- Acceso SSH al servidor
- Credenciales de root
- Dominio (opcional pero recomendado)

## Pasos de Deployment

### 1. Conectar al Servidor

```bash
ssh root@200.234.233.50
```

Contraseña: `DW!virus20`

### 2. Ejecutar Script de Setup

```bash
bash setup_complete.sh
```

Este script:
- Instala Apache, PHP, MySQL
- Crea la base de datos
- Configura permisos
- Inicia los servicios

### 3. Subir Archivos del Proyecto

```bash
bash deploy_to_server.sh 200.234.233.50
```

### 4. Configurar SSL/HTTPS

```bash
sudo apt-get install certbot python3-certbot-apache -y
sudo certbot --apache -d 200.234.233.50
```

### 5. Configurar Firewall

```bash
sudo ufw enable
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
```

### 6. Configurar Backups Automáticos

```bash
sudo nano /root/backup_mmcinema.sh
```

Pega el contenido del script de backup y luego:

```bash
sudo chmod +x /root/backup_mmcinema.sh
sudo crontab -e
```

Agrega:
```
0 2 * * * /root/backup_mmcinema.sh
```

## Verificación

Accede a `https://200.234.233.50` y verifica que el sitio carga correctamente.

## Configuración Pendiente

Ver [Configuración del Servidor](SERVER_CONFIG.md) para tareas adicionales.

---

**Última actualización**: 30 de Abril de 2026
