#!/bin/bash

# ============================================
# SCRIPT DE DEPLOY - MMCINEMA ADMIN PANEL
# ============================================

echo "╔════════════════════════════════════════════════════════════╗"
echo "║                                                            ║"
echo "║         🚀 DEPLOY PANEL ADMIN MEJORADO - MMCINEMA         ║"
echo "║                                                            ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Pedir datos del servidor
read -p "📍 IP del servidor: " SERVER_IP
read -p "👤 Usuario SSH (ej: root): " SSH_USER
read -sp "🔐 Contraseña SSH: " SSH_PASS
echo ""
read -p "📁 Ruta del proyecto en servidor (ej: /var/www/mmcinema): " PROJECT_PATH

# Validar que no estén vacíos
if [ -z "$SERVER_IP" ] || [ -z "$SSH_USER" ] || [ -z "$SSH_PASS" ] || [ -z "$PROJECT_PATH" ]; then
    echo "❌ Error: Todos los campos son requeridos"
    exit 1
fi

echo ""
echo "📦 Preparando archivos para deploy..."
echo ""

# Crear archivo tar con los cambios
tar -czf mmcinema_admin_fix_$(date +%Y%m%d_%H%M%S).tar.gz \
    admin/admin_header.php \
    admin/pages/dashboard/index.php \
    admin/crud/form.php \
    admin/pages/series/panel.php \
    assets/css/admin.css \
    assets/css/admin-alerts.css \
    assets/js/admin-search.js \
    assets/js/admin-forms.js \
    2>/dev/null

ARCHIVE=$(ls -t mmcinema_admin_fix_*.tar.gz | head -1)

echo "✅ Archivo creado: $ARCHIVE"
echo ""
echo "📤 Subiendo archivos al servidor..."
echo ""

# Subir archivo
sshpass -p "$SSH_PASS" scp -o StrictHostKeyChecking=no "$ARCHIVE" "$SSH_USER@$SERVER_IP:/tmp/"

if [ $? -eq 0 ]; then
    echo "✅ Archivo subido correctamente"
    echo ""
    echo "🔧 Aplicando cambios en el servidor..."
    echo ""
    
    # Ejecutar comandos en el servidor
    sshpass -p "$SSH_PASS" ssh -o StrictHostKeyChecking=no "$SSH_USER@$SERVER_IP" << EOF
        cd $PROJECT_PATH
        tar -xzf /tmp/$ARCHIVE
        echo "✅ Archivos extraídos"
        echo ""
        echo "📊 Cambios aplicados:"
        echo "  ✏️  admin/admin_header.php"
        echo "  ✏️  admin/pages/dashboard/index.php"
        echo "  ✏️  admin/crud/form.php"
        echo "  ✏️  admin/pages/series/panel.php"
        echo "  ✏️  assets/css/admin.css"
        echo "  ✏️  assets/css/admin-alerts.css"
        echo "  ✨ assets/js/admin-search.js"
        echo "  ✨ assets/js/admin-forms.js"
        echo ""
        echo "🧹 Limpiando archivos temporales..."
        rm /tmp/$ARCHIVE
        echo "✅ Limpieza completada"
EOF
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "╔════════════════════════════════════════════════════════════╗"
        echo "║                                                            ║"
        echo "║              ✅ DEPLOY COMPLETADO CON ÉXITO ✅             ║"
        echo "║                                                            ║"
        echo "╚════════════════════════════════════════════════════════════╝"
        echo ""
        echo "🎉 Tu panel admin ha sido actualizado en el servidor"
        echo ""
        echo "📍 Accede a: http://$SERVER_IP/admin/pages/dashboard/index.php"
        echo ""
        echo "✨ Nuevas características:"
        echo "  • Navegación con iconos"
        echo "  • Dashboard reorganizado"
        echo "  • Formularios mejorados"
        echo "  • Búsqueda en tablas"
        echo "  • Alertas diferenciadas"
        echo "  • Panel de series mejorado"
        echo "  • Validación de formularios"
        echo ""
        
        # Limpiar archivo local
        rm "$ARCHIVE"
        echo "✅ Archivo local eliminado"
    else
        echo "❌ Error al aplicar cambios en el servidor"
        exit 1
    fi
else
    echo "❌ Error al subir archivo al servidor"
    exit 1
fi
