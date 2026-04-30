#!/bin/bash

# Script Completo de Setup - MMCinema en Clouding
# Ejecutar como root
# Uso: bash setup_complete.sh

set -e

echo "=========================================="
echo "🚀 SETUP COMPLETO - MMCinema"
echo "=========================================="

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Variables
DB_NAME="mmcinema_prod"
DB_USER="mmcinema_user"
DB_PASS="mmcinema_pass_2026"
DB_ROOT_PASS="mmcinema_root_2026"
PROJECT_PATH="/var/www/html/mmcinema"
IP="200.234.233.50"

# Función para imprimir errores
error_exit() {
    echo -e "${RED}❌ ERROR: $1${NC}"
    exit 1
}

# Función para imprimir éxito
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

echo -e "${YELLOW}[1/11] Limpiando instalación anterior...${NC}"
rm -rf "$PROJECT_PATH" 2>/dev/null || true
systemctl stop apache2 2>/dev/null || true
success "Limpieza completada"

echo -e "${YELLOW}[2/11] Actualizando sistema...${NC}"
apt update > /dev/null 2>&1 || error_exit "No se pudo actualizar apt"
apt upgrade -y > /dev/null 2>&1 || error_exit "No se pudo hacer upgrade"
success "Sistema actualizado"

echo -e "${YELLOW}[3/11] Instalando dependencias...${NC}"
apt install -y apache2 php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip mysql-server git curl wget > /dev/null 2>&1 || error_exit "No se pudieron instalar dependencias"
success "Dependencias instaladas"

echo -e "${YELLOW}[4/11] Habilitando mod_rewrite...${NC}"
a2enmod rewrite > /dev/null 2>&1 || error_exit "No se pudo habilitar mod_rewrite"
systemctl restart apache2 > /dev/null 2>&1 || error_exit "No se pudo reiniciar Apache"
success "mod_rewrite habilitado"

echo -e "${YELLOW}[5/11] Configurando MySQL...${NC}"
mysql -u root << MYSQL_EOF || error_exit "No se pudo configurar MySQL"
ALTER USER 'root'@'localhost' IDENTIFIED BY '${DB_ROOT_PASS}';
DROP DATABASE IF EXISTS ${DB_NAME};
CREATE DATABASE ${DB_NAME};
DROP USER IF EXISTS '${DB_USER}'@'localhost';
CREATE USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_EOF
success "MySQL configurado"

echo -e "${YELLOW}[6/11] Creando estructura de carpetas...${NC}"
mkdir -p "$PROJECT_PATH"
mkdir -p "$PROJECT_PATH"/{admin,backend,components,config,database,docs,helpers,includes,lib,logs,pages,scripts,storage,tests,vendor}
mkdir -p "$PROJECT_PATH"/assets/img/{posters,series,noticias,carrusel,logos,plataformas}
cd "$PROJECT_PATH" || error_exit "No se pudo cambiar a $PROJECT_PATH"
success "Carpetas creadas"

echo -e "${YELLOW}[7/11] Configurando permisos...${NC}"
chmod 755 "$PROJECT_PATH"
chmod 755 "$PROJECT_PATH"/{admin,backend,components,config,database,docs,helpers,includes,lib,pages,scripts,tests,vendor,assets}
chmod 777 "$PROJECT_PATH"/logs
chmod 777 "$PROJECT_PATH"/storage
chmod 777 "$PROJECT_PATH"/assets/img
chmod 777 "$PROJECT_PATH"/assets/img/{posters,series,noticias,carrusel,logos,plataformas}
chown -R www-data:www-data "$PROJECT_PATH"
success "Permisos configurados"

echo -e "${YELLOW}[8/11] Creando .env...${NC}"
cat > "$PROJECT_PATH/.env" << ENV_EOF
# Configuración de Base de Datos
DB_HOST=localhost
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}

# Configuración de Correo (Gmail SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=david.monzonlopez@gmail.com
MAIL_PASSWORD="xvzx cvwp syqf cxkk"
MAIL_FROM_EMAIL=david.monzonlopez@gmail.com
MAIL_FROM_NAME=MMCinema

# URL Base del Proyecto
BASE_URL=http://${IP}

# Entorno (development, production)
APP_ENV=production
APP_DEBUG=false
ENV_EOF
chmod 644 "$PROJECT_PATH/.env"
success ".env creado"

echo -e "${YELLOW}[9/11] Creando archivos básicos...${NC}"
cat > "$PROJECT_PATH/index.php" << 'PHP_EOF'
<?php
/**
 * Archivo de entrada principal
 * Incluye pages/index.php directamente
 */
chdir('pages');
require_once 'index.php';
PHP_EOF
chmod 644 "$PROJECT_PATH/index.php"
success "index.php creado"

echo -e "${YELLOW}[10/11] Configurando Apache VirtualHost...${NC}"
cat > /etc/apache2/sites-available/mmcinema.conf << APACHE_EOF
<VirtualHost *:80>
    ServerName ${IP}
    DocumentRoot ${PROJECT_PATH}

    <Directory ${PROJECT_PATH}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/mmcinema_error.log
    CustomLog \${APACHE_LOG_DIR}/mmcinema_access.log combined
</VirtualHost>
APACHE_EOF

a2ensite mmcinema.conf > /dev/null 2>&1 || error_exit "No se pudo habilitar sitio"
a2dissite 000-default.conf > /dev/null 2>&1 || true
apache2ctl configtest > /dev/null 2>&1 || error_exit "Configuración de Apache inválida"
systemctl restart apache2 > /dev/null 2>&1 || error_exit "No se pudo reiniciar Apache"
success "Apache configurado"

echo -e "${YELLOW}[11/11] Verificando instalación...${NC}"
if systemctl is-active --quiet apache2; then
    success "Apache está corriendo"
else
    error_exit "Apache no está corriendo"
fi

if mysql -u root -p"${DB_ROOT_PASS}" -e "SELECT 1" > /dev/null 2>&1; then
    success "MySQL está corriendo"
else
    error_exit "MySQL no está corriendo"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}✅ SETUP COMPLETADO EXITOSAMENTE${NC}"
echo "=========================================="
echo ""
echo -e "${BLUE}📊 Datos de Acceso:${NC}"
echo "  Base de Datos: ${DB_NAME}"
echo "  Usuario BD: ${DB_USER}"
echo "  Contraseña BD: ${DB_PASS}"
echo "  Contraseña MySQL root: ${DB_ROOT_PASS}"
echo ""
echo -e "${BLUE}🌐 Acceder a:${NC}"
echo "  http://${IP}"
echo ""
echo -e "${BLUE}📁 Ruta del proyecto:${NC}"
echo "  ${PROJECT_PATH}"
echo ""
echo -e "${BLUE}⚠️  PRÓXIMOS PASOS:${NC}"
echo "  1. Sube tu proyecto a ${PROJECT_PATH} (por SFTP o Git)"
echo "  2. Importa la BD:"
echo "     mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < database/mmcinema3.sql"
echo "  3. Recarga el navegador en http://${IP}"
echo ""
echo "=========================================="
