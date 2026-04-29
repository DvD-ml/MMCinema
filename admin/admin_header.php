<?php
require_once __DIR__ . "/auth.php";

if (!function_exists('mm_admin_link_active')) {
    function mm_admin_link_active(array $archivosActuales, string $paginaActual): string {
        return in_array($paginaActual, $archivosActuales, true) ? 'active' : '';
    }
}

$paginaActualAdmin = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-shell-topbar">
    <div class="admin-brand-wrap">
        <img src="logo/logo_admin.png" alt="MMCinema Admin" width="150">
    </div>

    <div class="admin-topbar-right">
        <a href="index.php" class="<?= mm_admin_link_active(['index.php'], $paginaActualAdmin) ?>">Resumen</a>
        <a href="carrusel_destacado.php" class="<?= mm_admin_link_active(['carrusel_destacado.php'], $paginaActualAdmin) ?>">Carrusel</a>
        <a href="peliculas.php" class="<?= mm_admin_link_active(['peliculas.php', 'pelicula_form.php'], $paginaActualAdmin) ?>">Películas</a>
        <a href="proyecciones.php" class="<?= mm_admin_link_active(['proyecciones.php', 'proyeccion_form.php'], $paginaActualAdmin) ?>">Proyecciones</a>
        <a href="salas.php" class="<?= mm_admin_link_active(['salas.php', 'sala_form.php'], $paginaActualAdmin) ?>">Salas</a>
        <a href="noticias.php" class="<?= mm_admin_link_active(['noticias.php', 'noticia_form.php'], $paginaActualAdmin) ?>">Noticias</a>
        <a href="usuarios.php" class="<?= mm_admin_link_active(['usuarios.php', 'usuario_form.php'], $paginaActualAdmin) ?>">Usuarios</a>
        <a href="criticas.php" class="<?= mm_admin_link_active(['criticas.php', 'criticas_series.php'], $paginaActualAdmin) ?>">Críticas</a>
        <a href="series_panel.php" class="<?= mm_admin_link_active([
            'series_panel.php',
            'series.php',
            'agregar_serie.php',
            'editar_serie.php',
            'temporadas.php',
            'agregar_temporada.php',
            'editar_temporada.php',
            'episodios.php',
            'agregar_episodio.php',
            'editar_episodio.php',
            'criticas_series.php'
        ], $paginaActualAdmin) ?>">Series</a>
        <a href="../pages/cartelera.php" target="_blank" rel="noopener">Ver web</a>
        <a href="../pages/logout.php" class="logout-link">Cerrar sesión</a>
    </div>
</div>
