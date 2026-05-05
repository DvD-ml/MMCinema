# ============================================
# SCRIPT DE DEPLOY - MMCINEMA ADMIN PANEL
# ============================================

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║                                                            ║" -ForegroundColor Cyan
Write-Host "║         🚀 DEPLOY PANEL ADMIN MEJORADO - MMCINEMA         ║" -ForegroundColor Cyan
Write-Host "║                                                            ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Pedir datos del servidor
$SERVER_IP = Read-Host "📍 IP del servidor"
$SSH_USER = Read-Host "👤 Usuario SSH (ej: root)"
$SSH_PASS = Read-Host "🔐 Contraseña SSH" -AsSecureString
$PROJECT_PATH = Read-Host "📁 Ruta del proyecto en servidor (ej: /var/www/mmcinema)"

# Convertir contraseña segura a texto plano
$BSTR = [System.Runtime.InteropServices.Marshal]::SecureStringToGlobalAllocUnicode($SSH_PASS)
$SSH_PASS_TEXT = [System.Runtime.InteropServices.Marshal]::PtrToStringUni($BSTR)
[System.Runtime.InteropServices.Marshal]::ZeroFreeGlobalAllocUnicode($BSTR)

# Validar que no estén vacíos
if ([string]::IsNullOrEmpty($SERVER_IP) -or [string]::IsNullOrEmpty($SSH_USER) -or [string]::IsNullOrEmpty($SSH_PASS_TEXT) -or [string]::IsNullOrEmpty($PROJECT_PATH)) {
    Write-Host "❌ Error: Todos los campos son requeridos" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "📦 Preparando archivos para deploy..." -ForegroundColor Yellow
Write-Host ""

# Crear archivo zip con los cambios
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$zipFile = "mmcinema_admin_fix_$timestamp.zip"

# Archivos a incluir
$filesToZip = @(
    "admin/admin_header.php",
    "admin/pages/dashboard/index.php",
    "admin/crud/form.php",
    "admin/pages/series/panel.php",
    "assets/css/admin.css",
    "assets/css/admin-alerts.css",
    "assets/js/admin-search.js",
    "assets/js/admin-forms.js"
)

# Crear ZIP
Compress-Archive -Path $filesToZip -DestinationPath $zipFile -Force

Write-Host "✅ Archivo creado: $zipFile" -ForegroundColor Green
Write-Host ""
Write-Host "📤 Subiendo archivos al servidor..." -ForegroundColor Yellow
Write-Host ""

# Crear comando SCP usando sshpass (si está disponible) o plink
$sshpassPath = "C:\Program Files\Git\usr\bin\sshpass.exe"
$plinkPath = "C:\Program Files\PuTTY\plink.exe"

if (Test-Path $sshpassPath) {
    # Usar sshpass
    & $sshpassPath -p $SSH_PASS_TEXT scp -o StrictHostKeyChecking=no $zipFile "$SSH_USER@$SERVER_IP`:/tmp/"
} elseif (Test-Path $plinkPath) {
    # Usar plink (PuTTY)
    Write-Host "⚠️  Usando PuTTY plink (requiere confirmación manual)" -ForegroundColor Yellow
    & $plinkPath -l $SSH_USER -pw $SSH_PASS_TEXT -P 22 $SERVER_IP "cd /tmp && echo 'Listo para recibir archivos'"
} else {
    Write-Host "❌ Error: No se encontró sshpass o plink" -ForegroundColor Red
    Write-Host "Instala Git Bash o PuTTY para continuar" -ForegroundColor Red
    exit 1
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Archivo subido correctamente" -ForegroundColor Green
    Write-Host ""
    Write-Host "🔧 Aplicando cambios en el servidor..." -ForegroundColor Yellow
    Write-Host ""
    
    # Ejecutar comandos en el servidor
    $commands = @"
cd $PROJECT_PATH
unzip -o /tmp/$zipFile
echo '✅ Archivos extraídos'
echo ''
echo '📊 Cambios aplicados:'
echo '  ✏️  admin/admin_header.php'
echo '  ✏️  admin/pages/dashboard/index.php'
echo '  ✏️  admin/crud/form.php'
echo '  ✏️  admin/pages/series/panel.php'
echo '  ✏️  assets/css/admin.css'
echo '  ✏️  assets/css/admin-alerts.css'
echo '  ✨ assets/js/admin-search.js'
echo '  ✨ assets/js/admin-forms.js'
echo ''
echo '🧹 Limpiando archivos temporales...'
rm /tmp/$zipFile
echo '✅ Limpieza completada'
"@

    if (Test-Path $sshpassPath) {
        & $sshpassPath -p $SSH_PASS_TEXT ssh -o StrictHostKeyChecking=no "$SSH_USER@$SERVER_IP" $commands
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
        Write-Host "║                                                            ║" -ForegroundColor Green
        Write-Host "║              ✅ DEPLOY COMPLETADO CON ÉXITO ✅             ║" -ForegroundColor Green
        Write-Host "║                                                            ║" -ForegroundColor Green
        Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
        Write-Host ""
        Write-Host "🎉 Tu panel admin ha sido actualizado en el servidor" -ForegroundColor Green
        Write-Host ""
        Write-Host "📍 Accede a: http://$SERVER_IP/admin/pages/dashboard/index.php" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "✨ Nuevas características:" -ForegroundColor Yellow
        Write-Host "  • Navegación con iconos" -ForegroundColor White
        Write-Host "  • Dashboard reorganizado" -ForegroundColor White
        Write-Host "  • Formularios mejorados" -ForegroundColor White
        Write-Host "  • Búsqueda en tablas" -ForegroundColor White
        Write-Host "  • Alertas diferenciadas" -ForegroundColor White
        Write-Host "  • Panel de series mejorado" -ForegroundColor White
        Write-Host "  • Validación de formularios" -ForegroundColor White
        Write-Host ""
        
        # Limpiar archivo local
        Remove-Item $zipFile -Force
        Write-Host "✅ Archivo local eliminado" -ForegroundColor Green
    } else {
        Write-Host "❌ Error al aplicar cambios en el servidor" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "❌ Error al subir archivo al servidor" -ForegroundColor Red
    exit 1
}
