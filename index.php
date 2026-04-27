<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

/* =========================================
   CARRUSEL PREMIUM - PELÍCULAS DESTACADAS
========================================= */
$sqlCarousel = "
    SELECT
        p.id,
        p.titulo,
        p.sinopsis,
        p.poster,
        p.fecha_estreno,
        p.duracion,
        p.edad,
        g.nombre AS genero,
        (
            SELECT ROUND(AVG(c.puntuacion), 1)
            FROM critica c
            WHERE c.id_pelicula = p.id
        ) AS media_puntuacion
    FROM pelicula p
    LEFT JOIN genero g ON g.id = p.id_genero
    WHERE p.fecha_estreno <= CURDATE()
    ORDER BY
        media_puntuacion DESC,
        p.fecha_estreno DESC,
        p.id DESC
    LIMIT 4
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
            $html .= '<span class="star on">★</span>';
        } elseif ($i == $full + 1 && $half) {
            $html .= '<span class="star half">★</span>';
        } else {
            $html .= '<span class="star off">★</span>';
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
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include "navbar.php"; ?>
<?php include "laterales.php"; ?>

<main class="home-page">

    <!-- =========================================
         HERO + CARRUSEL PREMIUM
    ========================================== -->
    <section class="premium-carousel-section">
        <div class="container">
            <div id="premiumCinemaCarousel" class="carousel slide premium-cinema-carousel" data-bs-ride="carousel">

                <?php if (!empty($carouselPeliculas)): ?>
                    <div class="carousel-indicators premium-indicators">
                        <?php foreach ($carouselPeliculas as $i => $pelicula): ?>
                            <button type="button"
                                    data-bs-target="#premiumCinemaCarousel"
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
                        <div class="premium-empty-hero">
                            <div class="premium-empty-overlay"></div>
                            <div class="premium-empty-content">
                                <span class="section-kicker">Bienvenido a MMCinema</span>
                                <h1>Tu experiencia de cine premium</h1>
                                <p>
                                    Descubre estrenos, próximos lanzamientos, noticias y toda la emoción
                                    de la gran pantalla en un solo lugar.
                                </p>
                                <div class="hero-btn-row">
                                    <a href="cartelera.php" class="btn btn-primary btn-lg">Ver cartelera</a>
                                    <a href="proximamente.php" class="btn btn-outline-light btn-lg">Próximos estrenos</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($carouselPeliculas as $i => $p): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <div class="premium-slide-card">
                                    <img
                                        src="img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>"
                                        class="premium-slide-bg"
                                        alt="<?= htmlspecialchars($p['titulo']) ?>"
                                    >

                                    <div class="premium-slide-overlay"></div>

                                    <div class="premium-slide-content">
                                        <h1><?= htmlspecialchars($p['titulo']) ?></h1>

                                        <div class="premium-meta">
                                            <span><?= htmlspecialchars($p['genero'] ?: 'Sin género') ?></span>
                                            <span><?= !empty($p['duracion']) ? (int)$p['duracion'] . ' min' : 'Duración no disponible' ?></span>
                                            <span><?= htmlspecialchars($p['edad'] ?: 'TP') ?></span>
                                            <span>Estreno: <?= date('d/m/Y', strtotime($p['fecha_estreno'])) ?></span>
                                        </div>

                                        <div class="premium-rating mb-3">
                                            <?= mm_stars($p['media_puntuacion']) ?>
                                        </div>

                                        <p class="premium-description">
                                            <?= htmlspecialchars(mb_strimwidth($p['sinopsis'] ?? '', 0, 280, '...')) ?>
                                        </p>

                                        <div class="hero-btn-row">
                                            <a href="pelicula.php?id=<?= (int)$p['id'] ?>" class="btn btn-primary btn-lg">
                                                Ver detalles
                                            </a>
                                            <a href="cartelera.php" class="btn btn-outline-light btn-lg">
                                                Ir a cartelera
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (count($carouselPeliculas) > 1): ?>
                    <button class="carousel-control-prev premium-control" type="button" data-bs-target="#premiumCinemaCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next premium-control" type="button" data-bs-target="#premiumCinemaCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
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
                                src="img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>"
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

                                <a href="pelicula.php?id=<?= (int)$p['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-cta-center">
                <a href="proximamente.php" class="btn btn-outline-light">Ver todos los estrenos</a>
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
                                src="img/noticias/<?= htmlspecialchars($img) ?>"
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

                                <a href="noticia.php?id=<?= (int)$n['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                    Saber más
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-cta-center">
                <a href="noticias.php" class="btn btn-outline-light">Ver todas las noticias</a>
            </div>
        </div>
    </section>

    



</main>

<?php include "footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>