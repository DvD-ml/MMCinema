<?php
require_once __DIR__ . "/auth.php";

if (!function_exists('mm_admin_link_active')) {
    function mm_admin_link_active(array $archivosActuales, string $paginaActual): string {
        return in_array($paginaActual, $archivosActuales, true) ? 'active' : '';
    }
}

$rutaActual = $_SERVER['PHP_SELF'] ?? '';
?>
<!-- Estilos y scripts de alertas personalizadas -->
<link rel="stylesheet" href="<?= htmlspecialchars(mm_url('assets/css/admin-alerts.css')) ?>">
<script src="<?= htmlspecialchars(mm_url('assets/js/admin-alerts.js')) ?>"></script>

<div class="admin-shell-topbar">
    <div class="admin-brand-wrap">
        <img src="<?= htmlspecialchars(mm_url('admin/logo/logo_admin.png')) ?>" alt="MMCinema Admin" width="150">
    </div>

    <div class="admin-topbar-right">
        <a href="<?= htmlspecialchars(mm_admin_url('dashboard/index.php')) ?>" class="<?= strpos($rutaActual, 'dashboard') !== false ? 'active' : '' ?>">Resumen</a>
        <a href="<?= htmlspecialchars(mm_admin_url('dashboard/carrusel_destacado.php')) ?>" class="<?= strpos($rutaActual, 'carrusel') !== false ? 'active' : '' ?>">Carrusel</a>
        <a href="<?= htmlspecialchars(mm_admin_url('peliculas/list.php')) ?>" class="<?= strpos($rutaActual, 'peliculas') !== false ? 'active' : '' ?>">Peliculas</a>
        <a href="<?= htmlspecialchars(mm_admin_url('proyecciones/list.php')) ?>" class="<?= strpos($rutaActual, 'proyecciones') !== false ? 'active' : '' ?>">Proyecciones</a>
        <a href="<?= htmlspecialchars(mm_admin_url('salas/list.php')) ?>" class="<?= strpos($rutaActual, 'salas') !== false ? 'active' : '' ?>">Salas</a>
        <a href="<?= htmlspecialchars(mm_admin_url('noticias/list.php')) ?>" class="<?= strpos($rutaActual, 'noticias') !== false ? 'active' : '' ?>">Noticias</a>
        <a href="<?= htmlspecialchars(mm_admin_url('usuarios/list.php')) ?>" class="<?= strpos($rutaActual, 'usuarios') !== false ? 'active' : '' ?>">Usuarios</a>
        <a href="<?= htmlspecialchars(mm_admin_url('criticas/list.php')) ?>" class="<?= strpos($rutaActual, 'criticas') !== false ? 'active' : '' ?>">Criticas</a>
        <a href="<?= htmlspecialchars(mm_admin_url('series/panel.php')) ?>" class="<?= strpos($rutaActual, 'series') !== false ? 'active' : '' ?>">Series</a>
        <a href="<?= htmlspecialchars(mm_url('pages/cartelera.php')) ?>" target="_blank" rel="noopener">Ver web</a>
        <a href="<?= htmlspecialchars(mm_url('pages/logout.php')) ?>" class="logout-link">Cerrar sesion</a>
    </div>
</div>
