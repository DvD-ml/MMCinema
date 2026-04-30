#!/bin/bash

# Script de Deployment - Sube proyecto a Clouding y configura todo
# Uso: bash deploy_to_server.sh <IP_SERVIDOR>

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Validar argumentos
if [ -z "$1" ]; then
    echo -e "${RED}❌ Uso: bash deploy_to_server.sh <IP_SERVIDOR>${NC}"
    echo "Ejemplo: bash deploy_to_server.sh 200.234.233.50"
    exit 1
fi

SERVER_IP="$1"
PROJECT_PATH="/var/www/html/mmcinema"
DB_NAME="mmcinema_prod"
DB_USER="mmcinema_user"
DB_PASS="mmcinema_pass_2026"

echo "=========================================="
echo "🚀 DEPLOYMENT A CLOUDING"
echo "=========================================="
echo ""
echo -e "${BLUE}Servidor: ${SERVER_IP}${NC}"
echo -e "${BLUE}Ruta: ${PROJECT_PATH}${NC}"
echo ""

# Paso 1: Ejecutar setup_complete.sh en el servidor
echo -e "${YELLOW}[1/4] Ejecutando setup en servidor...${NC}"
ssh root@"$SERVER_IP" 'bash -s' < setup_complete.sh || {
    echo -e "${RED}❌ Error al ejecutar setup en servidor${NC}"
    exit 1
}
echo -e "${GREEN}✅ Setup completado en servidor${NC}"
echo ""

# Paso 2: Subir archivos del proyecto
echo -e "${YELLOW}[2/4] Subiendo archivos del proyecto...${NC}"
echo "Esto puede tomar unos minutos..."

# Excluir carpetas innecesarias
rsync -avz \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.vscode' \
    --exclude='*.log' \
    --exclude='.env.example' \
    --exclude='setup_complete.sh' \
    --exclude='deploy_to_server.sh' \
    --exclude='DEPLOYMENT_GUIDE.md' \
    --exclude='PRE_DEPLOYMENT_CHECKLIST.md' \
    --exclude='*.md' \
    . root@"$SERVER_IP":"$PROJECT_PATH/" || {
    echo -e "${RED}❌ Error al subir archivos${NC}"
    exit 1
}
echo -e "${GREEN}✅ Archivos subidos${NC}"
echo ""

# Paso 3: Importar base de datos
echo -e "${YELLOW}[3/4] Importando base de datos...${NC}"
ssh root@"$SERVER_IP" "mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < ${PROJECT_PATH}/database/mmcinema3.sql" || {
    echo -e "${RED}❌ Error al importar base de datos${NC}"
    echo "Intenta manualmente:"
    echo "  ssh root@${SERVER_IP}"
    echo "  mysql -u ${DB_USER} -p${DB_PASS} ${DB_NAME} < ${PROJECT_PATH}/database/mmcinema3.sql"
    exit 1
}
echo -e "${GREEN}✅ Base de datos importada${NC}"
echo ""

# Paso 4: Verificar instalación
echo -e "${YELLOW}[4/4] Verificando instalación...${NC}"
ssh root@"$SERVER_IP" "curl -s http://localhost | head -20" > /dev/null 2>&1 && {
    echo -e "${GREEN}✅ Servidor respondiendo${NC}"
} || {
    echo -e "${YELLOW}⚠️  Servidor no responde aún (puede estar iniciando)${NC}"
}

echo ""
echo "=========================================="
echo -e "${GREEN}✅ DEPLOYMENT COMPLETADO${NC}"
echo "=========================================="
echo ""
echo -e "${BLUE}🌐 Accede a tu sitio:${NC}"
echo "  http://${SERVER_IP}"
echo ""
echo -e "${BLUE}📊 Datos de acceso:${NC}"
echo "  Base de Datos: ${DB_NAME}"
echo "  Usuario BD: ${DB_USER}"
echo "  Contraseña BD: ${DB_PASS}"
echo ""
echo -e "${BLUE}📁 Ruta en servidor:${NC}"
echo "  ${PROJECT_PATH}"
echo ""
echo -e "${BLUE}🔧 Próximos pasos:${NC}"
echo "  1. Abre http://${SERVER_IP} en tu navegador"
echo "  2. Verifica que todo funcione correctamente"
echo "  3. Accede al admin en http://${SERVER_IP}/admin"
echo "  4. (Opcional) Configura SSL/HTTPS con Let's Encrypt"
echo ""
echo "=========================================="
