# Script para subir los cambios corregidos al servidor via SCP.
# Ejecutalo desde PowerShell en esta carpeta. SCP pedira la password de root si no hay clave SSH configurada.

$server = "200.234.233.50"
$user = "root"
$remotePath = "/var/www/html/mmcinema"
$localRoot = "C:\xampp\htdocs\david\MMCINEMA"

$files = @(
    ".htaccess",
    "admin/admin_header.php",
    "admin/auth.php",
    "admin/crud/delete.php",
    "admin/crud/form.php",
    "admin/crud/save.php",
    "admin/helpers/series_admin_ui.php",
    "admin/pages/.htaccess",
    "admin/pages/criticas/delete.php",
    "admin/pages/criticas/form.php",
    "admin/pages/criticas/list.php",
    "admin/pages/criticas/save.php",
    "admin/pages/dashboard/carrusel_destacado.php",
    "admin/pages/dashboard/index.php",
    "admin/pages/noticias/delete.php",
    "admin/pages/noticias/form.php",
    "admin/pages/noticias/list.php",
    "admin/pages/noticias/save.php",
    "admin/pages/peliculas/delete.php",
    "admin/pages/peliculas/form.php",
    "admin/pages/peliculas/list.php",
    "admin/pages/peliculas/save.php",
    "admin/pages/proyecciones/api.php",
    "admin/pages/proyecciones/delete.php",
    "admin/pages/proyecciones/form.php",
    "admin/pages/proyecciones/list.php",
    "admin/pages/proyecciones/save.php",
    "admin/pages/salas/delete.php",
    "admin/pages/salas/form.php",
    "admin/pages/salas/list.php",
    "admin/pages/salas/save.php",
    "admin/pages/series/criticas/list.php",
    "admin/pages/series/delete.php",
    "admin/pages/series/episodios/delete.php",
    "admin/pages/series/episodios/form.php",
    "admin/pages/series/episodios/list.php",
    "admin/pages/series/episodios/save.php",
    "admin/pages/series/form.php",
    "admin/pages/series/list.php",
    "admin/pages/series/panel.php",
    "admin/pages/series/save.php",
    "admin/pages/series/temporadas/delete.php",
    "admin/pages/series/temporadas/form.php",
    "admin/pages/series/temporadas/list.php",
    "admin/pages/series/temporadas/save.php",
    "admin/pages/usuarios/delete.php",
    "admin/pages/usuarios/form.php",
    "admin/pages/usuarios/list.php",
    "admin/pages/usuarios/save.php",
    "assets/css/admin.css",
    "components/navbar.php"
)

Write-Host "Subiendo cambios a ${user}@${server}:${remotePath}" -ForegroundColor Cyan
Write-Host "Archivos: $($files.Count)" -ForegroundColor Cyan

foreach ($file in $files) {
    $localPath = Join-Path $localRoot $file
    $remoteFile = "${user}@${server}:${remotePath}/$file"

    if (-not (Test-Path $localPath)) {
        Write-Host "No existe localmente: $file" -ForegroundColor Red
        exit 1
    }

    Write-Host "Subiendo $file" -ForegroundColor Yellow
    scp -P 22 $localPath $remoteFile

    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error subiendo $file" -ForegroundColor Red
        exit $LASTEXITCODE
    }
}

Write-Host "Limpiando archivos debug/test de series en servidor" -ForegroundColor Yellow
ssh $user@$server "rm -f '$remotePath/admin/pages/series/debug.php' '$remotePath/admin/pages/series/debug_links.php' '$remotePath/admin/pages/series/test.php' '$remotePath/admin/pages/series/test_simple.php'"

Write-Host "Despliegue completado" -ForegroundColor Green
Write-Host "Prueba: http://200.234.233.50/admin/pages/series/panel.php" -ForegroundColor Green
