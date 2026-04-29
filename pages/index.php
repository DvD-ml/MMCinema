<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "../config/conexion.php";

/* =========================================
   CARRUSEL DESTACADO - DESDE BD
========================================= */
$sqlCarousel = "
    SELECT 
        c.*,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.titulo
            WHEN c.tipo = 'serie' THEN s.titulo
        END as titulo_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.sinopsis
            WHEN c.tipo = 'serie' THEN s.sinopsis
        END as sinopsis_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.duracion
            WHEN c.tipo = 'serie' THEN NULL
        END as duracion_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.edad
            WHEN c.tipo = 'serie' THEN s.edad
        END as edad_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.fecha_estreno
            WHEN c.tipo = 'serie' THEN s.fecha_estreno
        END as fecha_estreno_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN YEAR(p.fecha_estreno)
            WHEN c.tipo = 'serie' THEN YEAR(s.fecha_estreno)
        END as anio_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN gp.nombre
            WHEN c.tipo = 'serie' THEN gs.nombre
        END as genero_contenido,
        CASE 
            WHEN c.tipo = 'serie' THEN (
                SELECT COUNT(*) FROM temporada t WHERE t.id_serie = s.id
            )
            ELSE NULL
        END as total_temporadas,
        CASE 
            WHEN c.tipo = 'pelicula' THEN (
                SELECT ROUND(AVG(cr.puntuacion), 1)
                FROM critica cr
                WHERE cr.id_pelicula = p.id
            )
            WHEN c.tipo = 'serie' THEN (
                SELECT ROUND(AVG(cs.puntuacion), 1)
                FROM critica_serie cs
                WHERE cs.id_serie = s.id
            )
        END as media_puntuacion
    FROM carrusel_destacado c
    LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
    LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
    LEFT JOIN genero gp ON c.tipo = 'pelicula' AND p.id_genero = gp.id
    LEFT JOIN genero gs ON c.tipo = 'serie' AND s.id_genero = gs.id
    WHERE c.activo = 1 
    AND (c.fecha_inicio IS NULL OR c.fecha_inicio <= CURDATE())
    AND (c.fecha_fin IS NULL OR c.fecha_fin >= CURDATE())
    ORDER BY c.orden ASC, c.id DESC
    LIMIT 6
";
$stmCarousel = $pdo->prepare($sqlCarousel);
$stmCarousel->execute();
$carouselPeliculas = $stmCarousel->fetchAll(PDO::FETCH_ASSOC);

/* =========================================
   PRÓXIMOS ESTRENOS
========================================= */
$sqlProximas = "
    SELECT
        p.id,
        p.titulo,
        p.sinopsis,
        p.poster,
        p.fecha_estreno,
        p.duracion,
        p.edad,
        g.nombre AS genero
    FROM pelicula p
    LEFT JOIN genero g ON g.id = p.id_genero
    WHERE p.fecha_estreno > CURDATE()
    ORDER BY p.fecha_estreno ASC, p.id ASC
    LIMIT 4
";
$stmProximas = $pdo->prepare($sqlProximas);
$stmProximas->execute();
$proximas = $stmProximas->fetchAll(PDO::FETCH_ASSOC);

/* =========================================
   ÚLTIMAS NOTICIAS
========================================= */
$sqlNoticias = "
    SELECT id, titulo, contenido, imagen, publicado
    FROM noticia
    ORDER BY publicado DESC, id DESC
    LIMIT 3
";
$stmNoticias = $pdo->prepare($sqlNoticias);
$stmNoticias->execute();
$noticias = $stmNoticias->fetchAll(PDO::FETCH_ASSOC);

/* =========================================
   MEJOR USUARIO
   Criterio: más actividad total
   (tickets + críticas + favoritas)
========================================= */
$sqlMejorUsuario = "
    SELECT
        u.id,
        u.username,
        u.email,
        u.creado,
        COALESCE(t.total_tickets, 0) AS total_tickets,
        COALESCE(c.total_criticas, 0) AS total_criticas,
        COALESCE(f.total_favoritas, 0) AS total_favoritas,
        (
            COALESCE(t.total_tickets, 0) +
            COALESCE(c.total_criticas, 0) +
            COALESCE(f.total_favoritas, 0)
        ) AS puntuacion_total
    FROM usuario u
    LEFT JOIN (
        SELECT id_usuario, COUNT(*) AS total_tickets
        FROM ticket
        GROUP BY id_usuario
    ) t ON t.id_usuario = u.id
    LEFT JOIN (
        SELECT id_usuario, COUNT(*) AS total_criticas
        FROM critica
        GROUP BY id_usuario
    ) c ON c.id_usuario = u.id
    LEFT JOIN (
        SELECT id_usuario, COUNT(*) AS total_favoritas
        FROM favorito
        GROUP BY id_usuario
    ) f ON f.id_usuario = u.id
    ORDER BY puntuacion_total DESC, total_criticas DESC, total_tickets DESC, u.id ASC
    LIMIT 1
";
$stmMejorUsuario = $pdo->prepare($sqlMejorUsuario);
$stmMejorUsuario->execute();
$mejorUsuario = $stmMejorUsuario->fetch(PDO::FETCH_ASSOC);

/* =========================================
   ESTADÍSTICAS GENERALES
========================================= */
$totalCartelera = (int)$pdo->query("SELECT COUNT(*) FROM pelicula WHERE fecha_estreno <= CURDATE()")->fetchColumn();
$totalProximas  = (int)$pdo->query("SELECT COUNT(*) FROM pelicula WHERE fecha_estreno > CURDATE()")->fetchColumn();
$totalNoticias  = (int)$pdo->query("SELECT COUNT(*) FROM noticia")->fetchColumn();
$totalUsuarios  = (int)$pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn();

/* =========================================
   FUNCIÓN ESTRELLAS
========================================= */
function mm_stars($media): string
{
    if ($media === null) {
        return '<span class="text-muted small">Sin valoraciones</span>';
    }

    $m = round(((float)$media) * 2) / 2;
    $full = floor($m);
    $half = (($m - $full) == 0.5);

    $html = '<span class="stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full) {
            $html .= '<span class="star on">?</span>';
        } elseif ($i == $full + 1 && $half) {
            $html .= '<span class="star half">?</span>';
        } else {
            $html .= '<span class="star off">?</span>';
        }
    }
    $html .= '</span>';
    $html .= ' <small class="text-muted">(' . number_format((float)$media, 1) . '/5)</small>';

    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Inicio</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

<?php include "../components/navbar.php"; ?>
<?php include "../components/laterales.php"; ?>

<main class="home-page">

    <!-- =========================================
         HERO + CARRUSEL NETFLIX
    ========================================== -->
    <section class="netflix-hero-section">
        <div id="netflixCarousel" class="carousel slide netflix-carousel" data-bs-ride="carousel" data-bs-interval="6000">

            <!-- Indicadores de navegación -->
            <?php if (!empty($carouselPeliculas)): ?>
                <div class="carousel-indicators netflix-indicators">
                    <?php foreach ($carouselPeliculas as $i => $pelicula): ?>
                        <button type="button"
                                data-bs-target="#netflixCarousel"
                                data-bs-slide-to="<?= $i ?>"
                                class="<?= $i === 0 ? 'active' : '' ?>"
                                aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                                aria-label="Slide <?= $i + 1 ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="carousel-inner">
                <?php if (empty($carouselPeliculas)): ?>
                    <div class="carousel-item active">
                        <div class="netflix-slide">
                            <div class="netflix-slide-bg" style="background: linear-gradient(135deg, #e50914, #221f1f);"></div>
                            <div class="netflix-slide-overlay"></div>
                            <div class="netflix-slide-content">
                                <div class="netflix-logo">
                                    <h1>MMCinema</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($carouselPeliculas as $i => $p): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <div class="netflix-slide">
                                <img src="../assets/img/carrusel/<?= htmlspecialchars($p['imagen_fondo']) ?>"
                                    class="netflix-slide-bg"
                                    alt="<?= htmlspecialchars($p['titulo']) ?>"
                                    style="object-position: <?= htmlspecialchars($p['imagen_posicion'] ?? 'center') ?>;"
                                >
                                <div class="netflix-slide-overlay"></div>
                                
                                <!-- Badge de categoría con texto dinámico -->
                                <?php 
                                $categorias_con_badge = ['proximamente', 'nueva_temporada', 'nuevo_episodio'];
                                $categoria_lower = strtolower($p['categoria']);
                                if (in_array($categoria_lower, $categorias_con_badge)): 
                                    // Determinar el texto del badge según la categoría
                                    $badge_texto = '';
                                    if ($categoria_lower === 'proximamente' && $p['fecha_estreno_contenido']) {
                                        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                        $mes = $meses[date('n', strtotime($p['fecha_estreno_contenido'])) - 1];
                                        $badge_texto = 'Estreno el ' . date('d', strtotime($p['fecha_estreno_contenido'])) . ' de ' . $mes;
                                    } elseif ($categoria_lower === 'nueva_temporada') {
                                        $badge_texto = 'Nueva Temporada';
                                    } elseif ($categoria_lower === 'nuevo_episodio') {
                                        $badge_texto = 'Nuevo Episodio';
                                    }
                                    
                                    if ($badge_texto):
                                ?>
                                    <div class="netflix-category-badge">
                                        <?= $badge_texto ?>
                                    </div>
                                <?php 
                                    endif;
                                endif; 
                                ?>
                                
                                <div class="netflix-slide-content">
                                    <div class="netflix-logo">
                                        <?php if ($p['logo_titulo']): ?>
                                            <img src="../assets/img/logos/<?= htmlspecialchars($p['logo_titulo']) ?>" 
                                                 alt="<?= htmlspecialchars($p['titulo']) ?>"
                                                 class="netflix-logo-img">
                                        <?php else: ?>
                                            <h1><?= htmlspecialchars($p['titulo']) ?></h1>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Información del contenido -->
                                    <div class="netflix-info">
                                        <div class="netflix-meta">
                                            <span class="netflix-type"><?= $p['tipo'] === 'pelicula' ? 'Película' : 'Serie' ?></span>
                                            <?php if ($p['genero_contenido']): ?>
                                                <span class="netflix-separator">•</span>
                                                <span class="netflix-genre"><?= htmlspecialchars($p['genero_contenido']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($p['anio_contenido']): ?>
                                                <span class="netflix-separator">•</span>
                                                <span class="netflix-year"><?= $p['anio_contenido'] ?></span>
                                            <?php endif; ?>
                                            <?php if ($p['tipo'] === 'serie' && $p['total_temporadas']): ?>
                                                <span class="netflix-separator">•</span>
                                                <span class="netflix-seasons"><?= $p['total_temporadas'] ?> temporada<?= $p['total_temporadas'] > 1 ? 's' : '' ?></span>
                                            <?php endif; ?>
                                            <?php if ($p['tipo'] === 'pelicula' && $p['duracion_contenido']): ?>
                                                <span class="netflix-separator">•</span>
                                                <span class="netflix-duration"><?= $p['duracion_contenido'] ?> min</span>
                                            <?php endif; ?>
                                            <?php if ($p['edad_contenido']): ?>
                                                <span class="netflix-separator">•</span>
                                                <span class="netflix-rating"><?= htmlspecialchars($p['edad_contenido']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- =========================================
         ESTADÍSTICAS
    ========================================== -->
    <section class="home-section">
        <div class="container">
            <div class="home-stats-grid">
                <div class="home-stat-card">
                    <strong><?= $totalCartelera ?></strong>
                    <span>Películas en cartelera</span>
                </div>
                <div class="home-stat-card">
                    <strong><?= $totalProximas ?></strong>
                    <span>Próximos estrenos</span>
                </div>
                <div class="home-stat-card">
                    <strong><?= $totalNoticias ?></strong>
                    <span>Noticias publicadas</span>
                </div>
                <div class="home-stat-card">
                    <strong><?= $totalUsuarios ?></strong>
                    <span>Usuarios registrados</span>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
         PRÓXIMOS ESTRENOS
    ========================================== -->
    <section class="home-section">
        <div class="container">
            <div class="section-heading">
                <h2>Próximos estrenos</h2>
                <p>Las películas que llegarán próximamente a MMCinema.</p>
            </div>

            <div class="row">
                <?php if (empty($proximas)): ?>
                    <p class="text-center text-muted">No hay próximos estrenos disponibles.</p>
                <?php endif; ?>

                <?php foreach ($proximas as $p): ?>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card pelicula-card h-100">
                            <img
                                class="card-img-top"
                                src="../assets/img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>"
                                alt="<?= htmlspecialchars($p['titulo']) ?>"
                            >

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h5>

                                <p class="mb-2 text-muted small">
                                    <?= htmlspecialchars($p['genero'] ?: 'Sin género') ?>
                                </p>

                                <p class="mb-2 text-muted small">
                                    Estreno: <?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?>
                                </p>

                                <p class="card-text flex-grow-1">
                                    <?= htmlspecialchars(mb_strimwidth($p['sinopsis'] ?? '', 0, 120, '...')) ?>
                                </p>

                                <a href="../pages/pelicula.php?id=<?= (int)$p['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-cta-center">
                <a href="../pages/proximamente.php" class="btn btn-outline-light">Ver todos los estrenos</a>
            </div>
        </div>
    </section>

    <!-- =========================================
         MEJOR USUARIO
    ========================================== -->
    <section class="home-section">
        <div class="container">
            <div class="section-heading">
                <span class="section-kicker">Comunidad MMCinema</span>
                <h2>Usuario destacado</h2>
                <p>El perfil con mayor actividad dentro de la plataforma.</p>
            </div>

            <?php if (!empty($mejorUsuario) && (int)$mejorUsuario['puntuacion_total'] > 0): ?>
                <?php $inicial = strtoupper(substr($mejorUsuario['username'], 0, 1)); ?>
                <div class="best-user-box">
                    <div class="best-user-avatar"><?= htmlspecialchars($inicial) ?></div>

                    <div class="best-user-content">
                        <span class="best-user-badge">Top usuario</span>
                        <h3><?= htmlspecialchars($mejorUsuario['username']) ?></h3>

                        <div class="best-user-stats">
                            <span><?= (int)$mejorUsuario['total_tickets'] ?> tickets</span>
                            <span><?= (int)$mejorUsuario['total_criticas'] ?> críticas</span>
                            <span><?= (int)$mejorUsuario['total_favoritas'] ?> favoritas</span>
                        </div>

                        <p class="best-user-text">
                            Miembro activo de la comunidad MMCinema, destacando por su participación,
                            valoraciones y seguimiento de películas.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="best-user-box best-user-empty">
                    <div class="best-user-content text-center w-100">
                        <h3>Todavía no hay usuario destacado</h3>
                        <p class="best-user-text mb-0">
                            Cuando los usuarios empiecen a reservar, valorar y guardar películas,
                            aquí aparecerá el perfil más activo.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- =========================================
         ÚLTIMAS NOTICIAS
    ========================================== -->
    <section class="home-section">
        <div class="container">
            <div class="section-heading">
                <h2>Últimas noticias</h2>
                <p>Las novedades más recientes del universo del cine en MMCinema.</p>
            </div>

            <div class="row">
                <?php if (empty($noticias)): ?>
                    <p class="text-center text-muted">No hay noticias publicadas todavía.</p>
                <?php endif; ?>

                <?php foreach ($noticias as $n): ?>
                    <?php $img = !empty($n['imagen']) ? $n['imagen'] : 'noticia-placeholder.jpg'; ?>
                    <div class="col-md-6 col-xl-4 mb-4">
                        <article class="card noticia-card h-100">
                            <img
                                src="../assets/img/noticias/<?= htmlspecialchars($img) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($n['titulo']) ?>"
                            >

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($n['titulo']) ?></h5>

                                <p class="text-muted small mb-2">
                                    Publicado: <?= date('d/m/Y', strtotime($n['publicado'])) ?>
                                </p>

                                <p class="card-text flex-grow-1">
                                    <?= htmlspecialchars(mb_strimwidth($n['contenido'], 0, 150, '...')) ?>
                                </p>

                                <a href="../pages/noticia.php?id=<?= (int)$n['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                    Saber más
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-cta-center">
                <a href="../pages/noticias.php" class="btn btn-outline-light">Ver todas las noticias</a>
            </div>
        </div>
    </section>

</main>

<?php include "../components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php // include "../includes/lenis-scripts.php"; // Lenis desactivado ?>
</body>
</html>


