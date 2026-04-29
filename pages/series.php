<?php
header('Content-Type: text/html; charset=UTF-8');
require_once("../config/conexion.php");

function estrellasSerie($puntuacion) {
    $max = 5;
    $puntuacion = max(0, min(5, (float)$puntuacion));
    $full = floor($puntuacion);
    $half = ($puntuacion - $full) >= 0.5 ? 1 : 0;
    $empty = $max - $full - $half;

    $html = '<div class="stars">';
    for ($i = 0; $i < $full; $i++) $html .= '<span class="star on">★</span>';
    if ($half) $html .= '<span class="star half">★</span>';
    for ($i = 0; $i < $empty; $i++) $html .= '<span class="star off">★</span>';
    $html .= '</div>';

    return $html;
}

function mm_build_series_url(array $queryBase, int $paginaDestino): string {
    $queryBase['pagina'] = $paginaDestino;
    return 'series.php?' . http_build_query($queryBase);
}

$plataformaFiltro = isset($_GET['plataforma']) ? (int)$_GET['plataforma'] : 0;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$porPagina = 6;
$offset = ($pagina - 1) * $porPagina;

$plataformas = $pdo->query("SELECT * FROM plataforma ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

$plataformaActual = null;
if ($plataformaFiltro > 0) {
    $stmtPlataforma = $pdo->prepare("SELECT * FROM plataforma WHERE id = ? LIMIT 1");
    $stmtPlataforma->execute([$plataformaFiltro]);
    $plataformaActual = $stmtPlataforma->fetch(PDO::FETCH_ASSOC) ?: null;

    if (!$plataformaActual) {
        $plataformaFiltro = 0;
        $pagina = 1;
        $offset = 0;
    }
}

/* DESTACADAS GENERALES */
$sqlDestacadas = "
    SELECT
        s.*,
        p.nombre AS plataforma_nombre,
        p.logo AS plataforma_logo,
        g.nombre AS genero_nombre,
        COALESCE(AVG(cs.puntuacion), 0) AS puntuacion_media,
        COUNT(cs.id) AS total_criticas
    FROM serie s
    LEFT JOIN plataforma p ON s.id_plataforma = p.id
    LEFT JOIN genero g ON s.id_genero = g.id
    LEFT JOIN critica_serie cs ON cs.id_serie = s.id
    GROUP BY s.id
    ORDER BY puntuacion_media DESC, total_criticas DESC, s.fecha_estreno DESC, s.id DESC
    LIMIT 5
";
$destacadas = $pdo->query($sqlDestacadas)->fetchAll(PDO::FETCH_ASSOC);

$series = [];
$totalSeries = 0;
$totalPaginas = 1;
$topPlataforma = [];

/* FILTRO POR PLATAFORMA */
if ($plataformaFiltro > 0) {
    $sqlCount = "SELECT COUNT(*) FROM serie WHERE id_plataforma = ?";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([$plataformaFiltro]);
    $totalSeries = (int)$stmtCount->fetchColumn();

    $totalPaginas = max(1, (int)ceil($totalSeries / $porPagina));
    if ($pagina > $totalPaginas) {
        $pagina = $totalPaginas;
        $offset = ($pagina - 1) * $porPagina;
    }

    /* TOP DE ESA PLATAFORMA */
    $sqlTopPlataforma = "
        SELECT
            s.*,
            p.nombre AS plataforma_nombre,
            p.logo AS plataforma_logo,
            g.nombre AS genero_nombre,
            COALESCE(AVG(cs.puntuacion), 0) AS puntuacion_media,
            COUNT(cs.id) AS total_criticas
        FROM serie s
        LEFT JOIN plataforma p ON s.id_plataforma = p.id
        LEFT JOIN genero g ON s.id_genero = g.id
        LEFT JOIN critica_serie cs ON cs.id_serie = s.id
        WHERE s.id_plataforma = ?
        GROUP BY s.id
        ORDER BY puntuacion_media DESC, total_criticas DESC, s.fecha_estreno DESC, s.id DESC
        LIMIT 8
    ";
    $stmtTopPlataforma = $pdo->prepare($sqlTopPlataforma);
    $stmtTopPlataforma->execute([$plataformaFiltro]);
    $topPlataforma = $stmtTopPlataforma->fetchAll(PDO::FETCH_ASSOC);

    /* TODAS LAS SERIES DE ESA PLATAFORMA, 6 POR PÁGINA */
    $sqlSeries = "
        SELECT
            s.*,
            p.nombre AS plataforma_nombre,
            p.logo AS plataforma_logo,
            g.nombre AS genero_nombre,
            COALESCE(AVG(cs.puntuacion), 0) AS puntuacion_media,
            COUNT(cs.id) AS total_criticas
        FROM serie s
        LEFT JOIN plataforma p ON s.id_plataforma = p.id
        LEFT JOIN genero g ON s.id_genero = g.id
        LEFT JOIN critica_serie cs ON cs.id_serie = s.id
        WHERE s.id_plataforma = ?
        GROUP BY s.id
        ORDER BY puntuacion_media DESC, total_criticas DESC, s.fecha_estreno DESC, s.id DESC
        LIMIT $porPagina OFFSET $offset
    ";
    $stmtSeries = $pdo->prepare($sqlSeries);
    $stmtSeries->execute([$plataformaFiltro]);
    $series = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);
}

$seriesPorPlataforma = [];
if ($plataformaFiltro === 0) {
    foreach ($plataformas as $plataforma) {
        $stmt = $pdo->prepare("
            SELECT
                s.*,
                p.nombre AS plataforma_nombre,
                p.logo AS plataforma_logo,
                COALESCE(AVG(cs.puntuacion), 0) AS puntuacion_media,
                COUNT(cs.id) AS total_criticas
            FROM serie s
            LEFT JOIN plataforma p ON s.id_plataforma = p.id
            LEFT JOIN critica_serie cs ON cs.id_serie = s.id
            WHERE s.id_plataforma = ?
            GROUP BY s.id
            ORDER BY puntuacion_media DESC, total_criticas DESC, s.fecha_estreno DESC, s.id DESC
            LIMIT 8
        ");
        $stmt->execute([$plataforma["id"]]);
        $seriesPorPlataforma[$plataforma["id"]] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$queryBase = $_GET;
unset($queryBase['pagina']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Series | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
<link rel="stylesheet" href="../assets/css/styles.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("../components/navbar.php"); ?>

<main class="series-page">

    <section class="home-section" id="bloque-plataformas">
        <div class="container">
            <div class="section-heading">
                <h2>Plataformas</h2>
                <p>Haz clic en una plataforma para ver sus series mejor valoradas y todo su catálogo.</p>
            </div>

            <div class="streaming-platforms">
                <?php foreach ($plataformas as $plataforma): ?>
                    <?php
                        $esActiva = $plataformaFiltro === (int)$plataforma['id'];
                        $link = $esActiva ? "series.php" : "series.php?plataforma=" . (int)$plataforma['id'];
                    ?>
                    <a href="<?= htmlspecialchars($link) ?>" class="streaming-pill <?= $esActiva ? 'active' : '' ?>">
                        <?php if (!empty($plataforma['logo'])): ?>
                            <img src="../<?= htmlspecialchars($plataforma['logo']) ?>" alt="<?= htmlspecialchars($plataforma['nombre']) ?>">
                        <?php else: ?>
                            <span class="streaming-pill-text"><?= htmlspecialchars($plataforma['nombre']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($plataformaFiltro === 0 && !empty($destacadas)): ?>
    <section class="home-section" id="series-destacadas">
        <div class="container">
            <div class="section-heading-left">
                <h2>Mejores Series</h2>
                <p>Valoradas por nuestros usuarios</p>
            </div>

            <div class="series-featured-slider">
                <div class="series-featured-track">
                    <?php
                    $destacadasCarrusel = count($destacadas) > 1 ? array_merge($destacadas, $destacadas) : $destacadas;
                    foreach ($destacadasCarrusel as $serie):
                    ?>
                        <article class="series-featured-item">
                            <div class="card pelicula-card serie-card h-100">
                                <img src="../<?= htmlspecialchars($serie['poster']) ?>" alt="<?= htmlspecialchars($serie['titulo']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($serie['titulo']) ?></h5>

                                    <div class="serie-card-meta">
                                        <span><?= htmlspecialchars($serie['genero_nombre'] ?? 'Sin género') ?></span>
                                        <span><?= !empty($serie['fecha_estreno']) ? date("Y", strtotime($serie['fecha_estreno'])) : 'Sin fecha' ?></span>
                                    </div>

                                    <div class="premium-rating mb-2">
                                        <?= estrellasSerie((float)$serie['puntuacion_media']) ?>
                                        <small><?= number_format((float)$serie['puntuacion_media'], 1) ?>/5</small>
                                    </div>

                                    <small><?= (int)$serie['total_criticas'] ?> crítica(s)</small>

                                    <a href="../pages/serie.php?id=<?= (int)$serie['id'] ?>" class="btn btn-primary w-100">
                                        Ver serie
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($plataformaFiltro > 0): ?>

        <?php if (!empty($topPlataforma)): ?>
        <section class="home-section" id="series-plataforma-top">
            <div class="container">
                <div class="section-heading-left">
                    <h2>Mejores valoradas de <?= htmlspecialchars($plataformaActual['nombre'] ?? 'la plataforma') ?></h2>
                    <p>Estas son las series top ahora mismo en esta plataforma.</p>
                </div>

                <div class="series-featured-slider">
                    <div class="series-featured-track">
                        <?php
                        $topCarrusel = count($topPlataforma) > 1 ? array_merge($topPlataforma, $topPlataforma) : $topPlataforma;
                        foreach ($topCarrusel as $serie):
                        ?>
                            <article class="series-featured-item">
                                <div class="card pelicula-card serie-card h-100">
                                    <img src="../<?= htmlspecialchars($serie['poster']) ?>" alt="<?= htmlspecialchars($serie['titulo']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($serie['titulo']) ?></h5>

                                        <div class="serie-card-meta">
                                            <span><?= htmlspecialchars($serie['genero_nombre'] ?? 'Sin género') ?></span>
                                            <span><?= !empty($serie['fecha_estreno']) ? date("Y", strtotime($serie['fecha_estreno'])) : 'Sin fecha' ?></span>
                                        </div>

                                        <div class="premium-rating mb-2">
                                            <?= estrellasSerie((float)$serie['puntuacion_media']) ?>
                                            <small><?= number_format((float)$serie['puntuacion_media'], 1) ?>/5</small>
                                        </div>

                                        <small><?= (int)$serie['total_criticas'] ?> crítica(s)</small>

                                        <a href="../pages/serie.php?id=<?= (int)$serie['id'] ?>" class="btn btn-primary w-100">
                                            Ver serie
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <section class="home-section" id="series-plataforma">
            <div class="container">
                <div class="section-heading-left">
                    <h2>Todas las series de <?= htmlspecialchars($plataformaActual['nombre'] ?? 'la plataforma') ?></h2>
                    <p>Mostrando 6 por página.</p>
                </div>

                <div class="row g-4">
                    <?php if (!empty($series)): ?>
                        <?php foreach ($series as $serie): ?>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <div class="card pelicula-card serie-card h-100 reveal-card">
                                    <img src="../<?= htmlspecialchars($serie['poster']) ?>" alt="<?= htmlspecialchars($serie['titulo']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($serie['titulo']) ?></h5>

                                        <div class="serie-card-meta">
                                            <span><?= htmlspecialchars($serie['edad'] ?? 'TP') ?></span>
                                            <span><?= htmlspecialchars($serie['estado'] ?? 'Disponible') ?></span>
                                        </div>

                                        <div class="premium-rating mb-2">
                                            <?= estrellasSerie((float)$serie['puntuacion_media']) ?>
                                            <small><?= number_format((float)$serie['puntuacion_media'], 1) ?>/5</small>
                                        </div>

                                        <small><?= (int)$serie['total_criticas'] ?> crítica(s)</small>

                                        <a href="../pages/serie.php?id=<?= (int)$serie['id'] ?>" class="btn btn-primary w-100">
                                            Ver serie
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="perfil-vacio">No hay series disponibles para esta plataforma.</div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($totalPaginas > 1): ?>
                    <nav class="cine-pagination-wrap mt-5" aria-label="Paginación series por plataforma">
                        <div class="cine-pagination">
                            <?php if ($pagina > 1): ?>
                                <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_build_series_url($queryBase, $pagina - 1)) ?>#series-plataforma">&#10094;</a>
                            <?php else: ?>
                                <span class="cine-page cine-page-nav disabled">&#10094;</span>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <a class="cine-page cine-page-number <?= $i === $pagina ? 'active' : '' ?>"
                                   href="<?= htmlspecialchars(mm_build_series_url($queryBase, $i)) ?>#series-plataforma">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($pagina < $totalPaginas): ?>
                                <a class="cine-page cine-page-nav" href="<?= htmlspecialchars(mm_build_series_url($queryBase, $pagina + 1)) ?>#series-plataforma">&#10095;</a>
                            <?php else: ?>
                                <span class="cine-page cine-page-nav disabled">&#10095;</span>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            </div>
        </section>

    <?php else: ?>

        <?php foreach ($plataformas as $plataforma): ?>
            <?php $bloque = $seriesPorPlataforma[$plataforma["id"]] ?? []; ?>
            <?php if (!empty($bloque)): ?>
                <section class="home-section">
                    <div class="container">
                        <div class="section-heading-left section-heading-flex">
                            <div>
                                <h2><?= htmlspecialchars($plataforma["nombre"]) ?></h2>
                                <p>Series disponibles en <?= htmlspecialchars($plataforma["nombre"]) ?>.</p>
                            </div>
                        </div>

                        <div class="series-row-scroll">
                            <?php foreach ($bloque as $serie): ?>
                                <article class="serie-mini-card reveal-card">
                                    <a href="../pages/serie.php?id=<?= (int)$serie['id'] ?>" class="serie-mini-link">
                                        <div class="serie-mini-poster">
                                            <img src="../<?= htmlspecialchars($serie['poster']) ?>" alt="<?= htmlspecialchars($serie['titulo']) ?>">
                                        </div>
                                        <div class="serie-mini-body">
                                            <h3><?= htmlspecialchars($serie['titulo']) ?></h3>
                                            <div class="premium-rating mb-1">
                                                <?= estrellasSerie((float)$serie['puntuacion_media']) ?>
                                            </div>
                                            <small><?= number_format((float)$serie['puntuacion_media'], 1) ?>/5 · <?= (int)$serie['total_criticas'] ?> críticas</small>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php endif; ?>

</main>

<?php include("../components/footer.php"); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cards = document.querySelectorAll(".reveal-card");

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    entry.target.style.setProperty("--delay", `${index * 60}ms`);
                    entry.target.classList.add("is-visible");
                }
            });
        }, {
            threshold: 0.12
        });

        cards.forEach(card => observer.observe(card));

        /* DRAG EN CARRUSELES */
        const sliders = document.querySelectorAll(".series-featured-slider");

        sliders.forEach((slider) => {
            const track = slider.querySelector(".series-featured-track");
            if (!track) return;

            let isDown = false;
            let startX = 0;
            let currentTranslate = 0;
            let prevTranslate = 0;
            let animationPausedByDrag = false;

            function setTranslate(x) {
                track.style.transform = `translateX(${x}px)`;
            }

            function startDrag(x) {
                isDown = true;
                startX = x;
                slider.classList.add("dragging");
                track.style.animation = "none";

                const computedStyle = window.getComputedStyle(track);
                const matrix = new DOMMatrixReadOnly(computedStyle.transform);
                currentTranslate = matrix.m41;
                prevTranslate = currentTranslate;
                animationPausedByDrag = true;
            }

            function moveDrag(x) {
                if (!isDown) return;
                const diff = x - startX;
                currentTranslate = prevTranslate + diff;
                setTranslate(currentTranslate);
            }

            function endDrag() {
                if (!isDown) return;
                isDown = false;
                slider.classList.remove("dragging");

                prevTranslate = currentTranslate;

                setTimeout(() => {
                    track.style.transform = "";
                    track.style.animation = "";
                    animationPausedByDrag = false;
                }, 1200);
            }

            slider.addEventListener("mousedown", (e) => {
                e.preventDefault();
                startDrag(e.pageX);
            });

            window.addEventListener("mousemove", (e) => {
                moveDrag(e.pageX);
            });

            window.addEventListener("mouseup", () => {
                endDrag();
            });

            slider.addEventListener("mouseleave", () => {
                if (isDown) endDrag();
            });

            /* táctil móvil */
            slider.addEventListener("touchstart", (e) => {
                startDrag(e.touches[0].clientX);
            }, { passive: true });

            slider.addEventListener("touchmove", (e) => {
                moveDrag(e.touches[0].clientX);
            }, { passive: true });

            slider.addEventListener("touchend", () => {
                endDrag();
            });
        });
    });
</script>
<?php // include "../includes/lenis-scripts.php"; // Lenis desactivado ?>
</body>
</html>


