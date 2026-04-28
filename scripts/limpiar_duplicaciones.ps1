# Script para limpiar duplicaciones de imágenes
# Ejecutar en PowerShell como administrador

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║         🧹 LIMPIEZA DE DUPLICACIONES DE IMÁGENES              ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$basePath = "C:\xampp\htdocs\david\MMCINEMA"

# ============================================
# 1. ELIMINAR NOTICIAS DUPLICADAS
# ============================================
Write-Host "1️⃣  Eliminando noticias duplicadas..." -ForegroundColor Yellow
$noticiasPath = "$basePath\assets\img\noticias\noticias"
if (Test-Path $noticiasPath) {
    $files = Get-ChildItem -Path $noticiasPath -File
    Write-Host "   Archivos encontrados: $($files.Count)"
    
    try {
        Remove-Item -Recurse -Force $noticiasPath
        Write-Host "   ✅ Carpeta eliminada correctamente" -ForegroundColor Green
    } catch {
        Write-Host "   ❌ Error al eliminar: $_" -ForegroundColor Red
    }
} else {
    Write-Host "   ℹ️  La carpeta no existe" -ForegroundColor Blue
}
Write-Host ""

# ============================================
# 2. ELIMINAR SERIES DUPLICADAS
# ============================================
Write-Host "2️⃣  Eliminando series duplicadas..." -ForegroundColor Yellow
$seriesPath = "$basePath\assets\img\series\series"
if (Test-Path $seriesPath) {
    $files = Get-ChildItem -Path $seriesPath -File -Recurse
    Write-Host "   Archivos encontrados: $($files.Count)"
    
    try {
        Remove-Item -Recurse -Force $seriesPath
        Write-Host "   ✅ Carpeta eliminada correctamente" -ForegroundColor Green
    } catch {
        Write-Host "   ❌ Error al eliminar: $_" -ForegroundColor Red
    }
} else {
    Write-Host "   ℹ️  La carpeta no existe" -ForegroundColor Blue
}
Write-Host ""

# ============================================
# 3. ELIMINAR CARPETA IMG ANTIGUA
# ============================================
Write-Host "3️⃣  Eliminando carpeta img antigua..." -ForegroundColor Yellow
$imgPath = "$basePath\img"
if (Test-Path $imgPath) {
    $files = Get-ChildItem -Path $imgPath -File -Recurse
    Write-Host "   Archivos encontrados: $($files.Count)"
    
    try {
        Remove-Item -Recurse -Force $imgPath
        Write-Host "   ✅ Carpeta eliminada correctamente" -ForegroundColor Green
    } catch {
        Write-Host "   ❌ Error al eliminar: $_" -ForegroundColor Red
    }
} else {
    Write-Host "   ℹ️  La carpeta no existe" -ForegroundColor Blue
}
Write-Host ""

# ============================================
# 4. VERIFICAR ESTRUCTURA FINAL
# ============================================
Write-Host "4️⃣  Verificando estructura final..." -ForegroundColor Yellow
Write-Host ""

$carpetas = @(
    "assets\img\carrusel",
    "assets\img\logos",
    "assets\img\noticias",
    "assets\img\plataformas",
    "assets\img\posters",
    "assets\img\series\banners",
    "assets\img\series\posters",
    "assets\img\series\temporadas"
)

foreach ($carpeta in $carpetas) {
    $ruta = "$basePath\$carpeta"
    if (Test-Path $ruta) {
        $files = Get-ChildItem -Path $ruta -File
        $count = $files.Count
        Write-Host "   ✅ $carpeta - $count archivos" -ForegroundColor Green
    } else {
        Write-Host "   ❌ $carpeta - NO EXISTE" -ForegroundColor Red
    }
}

Write-Host ""

# ============================================
# 5. ESTADÍSTICAS
# ============================================
Write-Host "5️⃣  Estadísticas finales..." -ForegroundColor Yellow
Write-Host ""

$totalArchivos = 0
$totalTamaño = 0

foreach ($carpeta in $carpetas) {
    $ruta = "$basePath\$carpeta"
    if (Test-Path $ruta) {
        $files = Get-ChildItem -Path $ruta -File
        $tamaño = ($files | Measure-Object -Property Length -Sum).Sum
        
        $totalArchivos += $files.Count
        $totalTamaño += $tamaño
        
        $tamañoMB = [math]::Round($tamaño / 1MB, 2)
        Write-Host "   $carpeta: $($files.Count) archivos ($tamañoMB MB)"
    }
}

$totalTamañoMB = [math]::Round($totalTamaño / 1MB, 2)

Write-Host ""
Write-Host "   TOTAL: $totalArchivos archivos ($totalTamañoMB MB)" -ForegroundColor Cyan
Write-Host ""

# ============================================
# 6. RESUMEN
# ============================================
Write-Host "✨ Resumen de limpieza:" -ForegroundColor Green
Write-Host "   ✅ Noticias duplicadas eliminadas"
Write-Host "   ✅ Series duplicadas eliminadas"
Write-Host "   ✅ Carpeta img antigua eliminada"
Write-Host "   ✅ Estructura verificada"
Write-Host ""

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║              🎉 ¡LIMPIEZA COMPLETADA!                         ║" -ForegroundColor Green
Write-Host "║                                                                ║" -ForegroundColor Green
Write-Host "║  Tu proyecto está ahora más limpio y optimizado.              ║" -ForegroundColor Green
Write-Host "║  Próximo paso: Verifica en el navegador que todo funcione.    ║" -ForegroundColor Green
Write-Host "║                                                                ║" -ForegroundColor Green
Write-Host "║  http://localhost/david/MMCINEMA/                             ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Green
