# Despliegue en paquete para MMCINEMA.
# Sube un unico archivo .tar.gz y luego lo extrae en el servidor.
# Si no tienes clave SSH, pedira password una vez para scp y una vez para ssh.

$ErrorActionPreference = "Stop"

$server = "200.234.233.50"
$user = "root"
$remotePath = "/var/www/html/mmcinema"
$localRoot = "C:\xampp\htdocs\david\MMCINEMA"
$packageName = "mmcinema_admin_fix_$(Get-Date -Format 'yyyyMMdd_HHmmss').tar.gz"
$packagePath = Join-Path $env:TEMP $packageName
$remotePackage = "/tmp/$packageName"

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
    "assets/css/admin-alerts.css",
    "assets/css/admin.css",
    "assets/js/admin-alerts.js",
    "assets/js/admin-forms.js",
    "assets/js/admin-search.js",
    "pages/pelicula.php",
    "pages/serie.php",
    "components/navbar.php"
)

Set-Location $localRoot

foreach ($file in $files) {
    if (-not (Test-Path -LiteralPath (Join-Path $localRoot $file))) {
        throw "No existe localmente: $file"
    }
}

if (Test-Path -LiteralPath $packagePath) {
    Remove-Item -LiteralPath $packagePath -Force
}

Write-Host "Creando paquete: $packagePath" -ForegroundColor Cyan
& tar -czf $packagePath @files
if ($LASTEXITCODE -ne 0) {
    throw "No se pudo crear el paquete tar.gz"
}

Write-Host "Subiendo paquete al servidor: ${user}@${server}:$remotePackage" -ForegroundColor Cyan
& scp -P 22 $packagePath "${user}@${server}:$remotePackage"
if ($LASTEXITCODE -ne 0) {
    throw "Fallo subiendo el paquete por scp"
}

$remoteCommand = "set -e; mkdir -p '$remotePath'; tar -xzf '$remotePackage' -C '$remotePath'; rm -f '$remotePath/admin/pages/series/debug.php' '$remotePath/admin/pages/series/debug_links.php' '$remotePath/admin/pages/series/test.php' '$remotePath/admin/pages/series/test_simple.php'; rm -f '$remotePackage'; find '$remotePath/admin' -type d -exec chmod 755 {} \;; find '$remotePath/admin' -type f -exec chmod 644 {} \;; echo 'Deploy OK'"

Write-Host "Aplicando paquete en el servidor" -ForegroundColor Cyan
& ssh -p 22 "${user}@${server}" $remoteCommand
if ($LASTEXITCODE -ne 0) {
    throw "Fallo aplicando el paquete en el servidor"
}

Remove-Item -LiteralPath $packagePath -Force

Write-Host "Despliegue completado" -ForegroundColor Green
Write-Host "Prueba: http://200.234.233.50/admin/pages/series/panel.php" -ForegroundColor Green
