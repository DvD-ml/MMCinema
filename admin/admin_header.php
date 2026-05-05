<?php
require_once __DIR__ . "/auth.php";

if (!function_exists('mm_admin_link_active')) {
    function mm_admin_link_active(array $archivosActuales, string $paginaActual): string {
        return in_array($paginaActual, $archivosActuales, true) ? 'active' : '';
    }
}

$paginaActualAdmin = basename($_SERVER['PHP_SELF']);
$rutaActual = $_SERVER['PHP_SELF'];

// Calcular la profundidad desde admin/pages
$pathParts = explode('/', trim($_SERVER['PHP_SELF'], '/'));
$pagesIndex = array_search('pages', $pathParts, true);
if ($pagesIndex === false) {
    $depthFromPages = 1;
} else {
    $depthFromPages = count($pathParts) - $pagesIndex - 1;
}
$upLevels = str_repeat('../', $depthFromPages);
?>
<!-- Estilos y scripts de alertas personalizadas -->
<link rel="stylesheet" href="/assets/css/admin-alerts.css">
<script src="/assets/js/admin-alerts.js"></script>

<div class="admin-shell-topbar">
    <div class="admin-brand-wrap">
        <img src="/admin/logo/logo_admin.png" alt="MMCinema Admin" width="150">
    </div>

    <div class="admin-topbar-right">
        <a href="<?= $upLevels ?>dashboard/index.php" class="<?= strpos($rutaActual, 'dashboard') !== false ? 'active' : '' ?>">Resumen</a>
        <a href="<?= $upLevels ?>dashboard/carrusel_destacado.php" class="<?= strpos($rutaActual, 'carrusel') !== false ? 'active' : '' ?>">Carrusel</a>
        <a href="<?= $upLevels ?>peliculas/list.php" class="<?= strpos($rutaActual, 'peliculas') !== false ? 'active' : '' ?>">Películas</a>
        <a href="<?= $upLevels ?>proyecciones/list.php" class="<?= strpos($rutaActual, 'proyecciones') !== false ? 'active' : '' ?>">Proyecciones</a>
        <a href="<?= $upLevels ?>salas/list.php" class="<?= strpos($rutaActual, 'salas') !== false ? 'active' : '' ?>">Salas</a>
        <a href="<?= $upLevels ?>noticias/list.php" class="<?= strpos($rutaActual, 'noticias') !== false ? 'active' : '' ?>">Noticias</a>
        <a href="<?= $upLevels ?>usuarios/list.php" class="<?= strpos($rutaActual, 'usuarios') !== false ? 'active' : '' ?>">Usuarios</a>
        <a href="<?= $upLevels ?>criticas/list.php" class="<?= strpos($rutaActual, 'criticas') !== false ? 'active' : '' ?>">Críticas</a>
        <a href="<?= $upLevels ?>series/panel.php" class="<?= strpos($rutaActual, 'series') !== false ? 'active' : '' ?>">Series</a>
        <a href="<?= $upLevels ?>../../pages/cartelera.php" target="_blank" rel="noopener">Ver web</a>
        <a href="<?= $upLevels ?>../../pages/logout.php" class="logout-link">Cerrar sesión</a>
    </div>
</div>
