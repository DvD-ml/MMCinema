# Despliegue unico de MMCINEMA.
# Ejecuta: .\deploy.ps1

param(
    [string]$Server = "200.234.233.50",
    [string]$User = "root",
    [int]$Port = 22,
    [string]$RemotePath = "/var/www/html/mmcinema",
    [switch]$SkipMigrations
)

$ErrorActionPreference = "Stop"

$localRoot = $PSScriptRoot
if ([string]::IsNullOrWhiteSpace($localRoot)) {
    $localRoot = (Get-Location).Path
}

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$packageName = "mmcinema_deploy_$timestamp.tar.gz"
$packagePath = Join-Path $env:TEMP $packageName
$remotePackage = "/tmp/$packageName"

$excludeArgs = @(
    "--exclude=.git",
    "--exclude=.env",
    "--exclude=logs/*",
    "--exclude=storage/cache/*",
    "--exclude=storage/logs/*",
    "--exclude=storage/tickets/*.pdf",
    "--exclude=tickets/*.pdf",
    "--exclude=*.tmp",
    "--exclude=*.bak"
)

function Invoke-CheckedCommand {
    param(
        [Parameter(Mandatory = $true)]
        [scriptblock]$Command,
        [Parameter(Mandatory = $true)]
        [string]$ErrorMessage
    )

    & $Command
    if ($LASTEXITCODE -ne 0) {
        throw $ErrorMessage
    }
}

Set-Location -LiteralPath $localRoot

$requiredRuntimeFiles = @(
    "vendor/autoload.php",
    "vendor/composer/autoload_static.php",
    "vendor/composer/installed.php"
)

foreach ($requiredFile in $requiredRuntimeFiles) {
    if (-not (Test-Path -LiteralPath (Join-Path $localRoot $requiredFile))) {
        throw "Falta $requiredFile. Ejecuta composer install antes de desplegar."
    }
}

if (Test-Path -LiteralPath $packagePath) {
    Remove-Item -LiteralPath $packagePath -Force
}

Write-Host "Creando paquete unico: $packagePath" -ForegroundColor Cyan
Invoke-CheckedCommand `
    -Command { & tar -czf $packagePath @excludeArgs -C $localRoot . } `
    -ErrorMessage "No se pudo crear el paquete tar.gz"

Write-Host "Subiendo paquete a ${User}@${Server}:$remotePackage" -ForegroundColor Cyan
Invoke-CheckedCommand `
    -Command { & scp -P $Port $packagePath "${User}@${Server}:$remotePackage" } `
    -ErrorMessage "Fallo subiendo el paquete por scp"

$migrationCommand = ""
if (-not $SkipMigrations) {
    $migrationCommand = "if [ -f '$RemotePath/database/migrations/007_usuario_email_username_50.php' ]; then php '$RemotePath/database/migrations/007_usuario_email_username_50.php'; fi;"
}

$remoteCommand = @"
set -e
mkdir -p '$RemotePath'
tar -xzf '$remotePackage' -C '$RemotePath'
mkdir -p '$RemotePath/storage/cache' '$RemotePath/storage/logs' '$RemotePath/storage/tickets' '$RemotePath/assets/img/carrusel' '$RemotePath/assets/img/logos' '$RemotePath/assets/img/noticias' '$RemotePath/assets/img/posters' '$RemotePath/assets/img/series/banners' '$RemotePath/assets/img/series/posters' '$RemotePath/assets/img/series/temporadas'
$migrationCommand
rm -f '$RemotePath/admin/pages/series/debug.php' '$RemotePath/admin/pages/series/debug_links.php' '$RemotePath/admin/pages/series/test.php' '$RemotePath/admin/pages/series/test_simple.php'
rm -f '$remotePackage'
find '$RemotePath' -type d -exec chmod 755 {} \;
find '$RemotePath' -type f -exec chmod 644 {} \;
if id www-data >/dev/null 2>&1; then
  chown -R www-data:www-data '$RemotePath/storage' '$RemotePath/assets/img/carrusel' '$RemotePath/assets/img/logos' '$RemotePath/assets/img/noticias' '$RemotePath/assets/img/posters' '$RemotePath/assets/img/series'
fi
chmod -R 775 '$RemotePath/storage' '$RemotePath/assets/img/carrusel' '$RemotePath/assets/img/logos' '$RemotePath/assets/img/noticias' '$RemotePath/assets/img/posters' '$RemotePath/assets/img/series'
echo 'Deploy OK'
"@

$remoteCommand = $remoteCommand -replace "`r`n", "`n" -replace "`r", "`n"

Write-Host "Aplicando paquete en el servidor" -ForegroundColor Cyan
Invoke-CheckedCommand `
    -Command { & ssh -p $Port "${User}@${Server}" $remoteCommand } `
    -ErrorMessage "Fallo aplicando el paquete en el servidor"

Remove-Item -LiteralPath $packagePath -Force

Write-Host "Despliegue completado" -ForegroundColor Green
Write-Host "Web: http://$Server/" -ForegroundColor Green
Write-Host "Admin: http://$Server/admin/pages/dashboard/index.php" -ForegroundColor Green
