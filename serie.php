<?php
require_once("config/conexion.php");
if (session_status() === PHP_SESSION_NONE) session_start();

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

function mm_build_serie_platform_url(int $idSerie, int $paginaDestino): string {
    return 'serie.php?' . http_build_query([
        'id' => $idSerie,
        'pagina_plataforma' => $paginaDestino
    ]);
}

$idSerie = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$paginaPlataforma = isset($_GET['pagina_plataforma']) ? (int)$_GET['pagina_plataforma'] : 1;
if ($paginaPlataforma < 1) $paginaPlataforma = 1;

if ($idSerie <= 0) {
    die("Serie no válida.");
}

/* SERIE PRINCIPAL */
$stmtSerie = $pdo->prepare("
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
    WHERE s.id = ?
    GROUP BY s.id
    LIMIT 1
");
$stmtSerie->execute([$idSerie]);
$serie = $stmtSerie->fetch(PDO::FETCH_ASSOC);

if (!$serie) {
    die("La serie no existe.");
}

/* SERIES DE LA MISMA PLATAFORMA */
$seriesPlataformaTop = [];
$seriesPlataforma = [];
$totalSeriesPlataforma = 0;
$totalPaginasPlataforma = 1;
$porPaginaPlataforma = 6;
$offsetPlataforma = ($paginaPlataforma - 1) * $porPaginaPlataforma;

if (!empty($serie['id_plataforma'])) {
    $stmtCountPlataforma = $pdo->prepare("SELECT COUNT(*) FROM serie WHERE id_plataforma = ?");
    $stmtCountPlataforma->execute([(int)$serie['id_plataforma']]);
    $totalSeriesPlataforma = (int)$stmtCountPlataforma->fetchColumn();

    $totalPaginasPlataforma = max(1, (int)ceil($totalSeriesPlataforma / $porPaginaPlataforma));

    if ($paginaPlataforma > $totalPaginasPlataforma) {
        $paginaPlataforma = $totalPaginasPlataforma;
        $offsetPlataforma = ($paginaPlataforma - 1) * $porPaginaPlataforma;
    }

    /* CARRUSEL TOP DE LA PLATAFORMA */
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
        LIMIT 10
    ";
    $stmtTopPlataforma = $pdo->prepare($sqlTopPlataforma);
    $stmtTopPlataforma->execute([(int)$serie['id_plataforma']]);
    $seriesPlataformaTop = $stmtTopPlataforma->fetchAll(PDO::FETCH_ASSOC);

    /* TODAS LAS SERIES DE ESA PLATAFORMA, 6 POR PÁGINA */
    $sqlSeriesPlataforma = "
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
        LIMIT $porPaginaPlataforma OFFSET $offsetPlataforma
    ";
    $stmtSeriesPlataforma = $pdo->prepare($sqlSeriesPlataforma);
    $stmtSeriesPlataforma->execute([(int)$serie['id_plataforma']]);
    $seriesPlataforma = $stmtSeriesPlataforma->fetchAll(PDO::FETCH_ASSOC);
}

/* TEMPORADAS */
$stmtTemporadas = $pdo->prepare("
    SELECT *
    FROM temporada
    WHERE id_serie = ?
    ORDER BY numero_temporada ASC
");
$stmtTemporadas->execute([$idSerie]);
$temporadas = $stmtTemporadas->fetchAll(PDO::FETCH_ASSOC);

/* EPISODIOS */
$episodiosPorTemporada = [];

if (!empty($temporadas)) {
    $idsTemporadas = array_column($temporadas, 'id');
    $placeholders = implode(',', array_fill(0, count($idsTemporadas), '?'));

    $stmtEpisodios = $pdo->prepare("
        SELECT *
        FROM episodio
        WHERE id_temporada IN ($placeholders)
        ORDER BY id_temporada ASC, numero_episodio ASC
    ");
    $stmtEpisodios->execute($idsTemporadas);
    $episodios = $stmtEpisodios->fetchAll(PDO::FETCH_ASSOC);

    foreach ($episodios as $episodio) {
        $episodiosPorTemporada[$episodio['id_temporada']][] = $episodio;
    }
}

/* CRÍTICAS */
$stmtCriticas = $pdo->prepare("
    SELECT 
        cs.*, 
        u.username
    FROM critica_serie cs
    LEFT JOIN usuario u ON u.id = cs.id_usuario
    WHERE cs.id_serie = ?
    ORDER BY cs.creado DESC
");
$stmtCriticas->execute([$idSerie]);
$criticas = $stmtCriticas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($serie['titulo']) ?> | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/series.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("navbar.php"); ?>

<main class="serie-detalle-page">

    <section class="serie-hero-detalle" style="
        background:
        linear-gradient(90deg, rgba(7,10,18,.95) 0%, rgba(7,10,18,.75) 45%, rgba(7,10,18,.92) 100%),
        url('<?= !empty($serie['banner']) ? htmlspecialchars($serie['banner']) : htmlspecialchars($serie['poster']) ?>') center/cover no-repeat;
    ">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-md-4 col-lg-3">
                    <img
                        src="<?= htmlspecialchars($serie['poster']) ?>"
                        alt="<?= htmlspecialchars($serie['titulo']) ?>"
                        class="img-fluid rounded-4 shadow-lg"
                        style="width:100%; max-width:320px; object-fit:cover;"
                    >
                </div>

                <div class="col-md-8 col-lg-9 text-white">
                    <span class="badge bg-warning text-dark mb-3">
                        <?= htmlspecialchars($serie['plataforma_nombre'] ?? 'Plataforma') ?>
                    </span>

                    <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($serie['titulo']) ?></h1>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-dark-subtle text-light"><?= htmlspecialchars($serie['genero_nombre'] ?? 'Sin género') ?></span>
                        <span class="badge bg-dark-subtle text-light"><?= htmlspecialchars($serie['estado'] ?? 'Sin estado') ?></span>
                        <span class="badge bg-dark-subtle text-light"><?= htmlspecialchars($serie['edad'] ?? 'TP') ?></span>
                        <?php if (!empty($serie['fecha_estreno'])): ?>
                            <span class="badge bg-dark-subtle text-light"><?= date("Y", strtotime($serie['fecha_estreno'])) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="premium-rating mb-3">
                        <?= estrellasSerie((float)$serie['puntuacion_media']) ?>
                        <small class="ms-2 text-light">
                            <?= number_format((float)$serie['puntuacion_media'], 1) ?>/5 · <?= (int)$serie['total_criticas'] ?> crítica(s)
                        </small>
                    </div>

                    <p class="lead mb-4"><?= nl2br(htmlspecialchars($serie['sinopsis'])) ?></p>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="#temporadas" class="btn btn-primary">Ver temporadas</a>
                        <?php if (!empty($serie['trailer'])): ?>
                            <a href="<?= htmlspecialchars($serie['trailer']) ?>" target="_blank" class="btn btn-warning fw-semibold">
                                Ver tráiler
                            </a>
                        <?php endif; ?>

                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="serie-subnav-wrap">
        <div class="container">
            <div class="serie-subnav">
                <a href="series.php" class="serie-subnav-link">Volver a series</a>

                <a href="#temporadas" class="serie-subnav-link">Temporadas</a>

                <a href="#criticas-series" class="serie-subnav-link">Críticas</a>
            </div>
        </div>
    </section>

    <section class="home-section" id="temporadas">
        <div class="container">
            <div class="section-heading-left">
                <h2>Temporadas y episodios</h2>
                <p>Explora todo el contenido disponible de la serie.</p>
            </div>

            <?php if (!empty($temporadas)): ?>
                <div class="season-selector-wrap mb-4">
                    <label for="seasonSelect" class="form-label text-white fw-semibold">Selecciona una temporada</label>
                    <select id="seasonSelect" class="form-select season-select">
                        <?php foreach ($temporadas as $index => $temporada): ?>
                            <option value="season-panel-<?= (int)$temporada['id'] ?>" <?= $index === 0 ? 'selected' : '' ?>>
                                Temporada <?= (int)$temporada['numero_temporada'] ?>
                                <?= !empty($temporada['titulo']) ? ' · ' . htmlspecialchars($temporada['titulo']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="season-single-view">
                    <?php foreach ($temporadas as $index => $temporada): ?>
                        <?php $episodiosTemp = $episodiosPorTemporada[$temporada['id']] ?? []; ?>

                        <div class="season-panel-single <?= $index === 0 ? 'active' : '' ?>" id="season-panel-<?= (int)$temporada['id'] ?>">
                            <div class="season-panel-head">
                                <div class="season-toggle-left">
                                    <div>
                                        <h3>
                                            Temporada <?= (int)$temporada['numero_temporada'] ?>
                                            <?php if (!empty($temporada['titulo'])): ?>
                                                · <?= htmlspecialchars($temporada['titulo']) ?>
                                            <?php endif; ?>
                                        </h3>

                                        <?php if (!empty($temporada['fecha_estreno'])): ?>
                                            <p>Estreno: <?= htmlspecialchars($temporada['fecha_estreno']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="season-content-visible">
                                <div class="row g-4 align-items-start">
                                    <div class="col-md-3 col-lg-2">
                                        <?php if (!empty($temporada['poster'])): ?>
                                            <img
                                                src="<?= htmlspecialchars($temporada['poster']) ?>"
                                                alt="<?= htmlspecialchars($temporada['titulo'] ?: 'Temporada ' . $temporada['numero_temporada']) ?>"
                                                class="img-fluid rounded-4 season-poster"
                                            >
                                        <?php else: ?>
                                            <div class="season-poster-placeholder">
                                                T<?= (int)$temporada['numero_temporada'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-9 col-lg-10">
                                        <?php if (!empty($temporada['descripcion'])): ?>
                                            <p class="season-description">
                                                <?= nl2br(htmlspecialchars($temporada['descripcion'])) ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if (!empty($episodiosTemp)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-dark table-hover align-middle mb-0 season-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Episodio</th>
                                                            <th>Título</th>
                                                            <th>Duración</th>
                                                            <th>Fecha</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($episodiosTemp as $episodio): ?>
                                                            <tr>
                                                                <td><?= (int)$episodio['numero_episodio'] ?></td>
                                                                <td><?= htmlspecialchars($episodio['titulo']) ?></td>
                                                                <td><?= !empty($episodio['duracion']) ? (int)$episodio['duracion'] . ' min' : '—' ?></td>
                                                                <td><?= !empty($episodio['fecha_estreno']) ? htmlspecialchars($episodio['fecha_estreno']) : '—' ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-0">
                                                Esta temporada todavía no tiene episodios cargados.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="perfil-vacio">Todavía no hay temporadas registradas para esta serie.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="home-section" id="criticas-series">
        <div class="container">
            <div class="section-heading-left">
                <h2>Críticas de usuarios</h2>
                <p>Valoraciones y comentarios de la comunidad.</p>
            </div>

            <?php if (empty($criticas)): ?>
                <p>Sé el primero en dejar una crítica para esta serie.</p>
            <?php else: ?>
                <div class="row g-4 mb-4">
                    <?php foreach ($criticas as $critica): ?>
                        <div class="col-md-6">
                            <div class="card bg-dark text-white border-secondary h-100 rounded-4 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?= htmlspecialchars($critica['username'] ?: 'Anónimo') ?></strong>
                                        <span class="badge bg-warning text-dark"><?= (int)$critica['puntuacion'] ?>/5</span>
                                    </div>

                                    <div class="premium-rating mb-3">
                                        <?= estrellasSerie((float)$critica['puntuacion']) ?>
                                    </div>

                                    <p class="mb-3"><?= nl2br(htmlspecialchars($critica['contenido'])) ?></p>
                                    <small class="text-secondary">
                                        <?= !empty($critica['creado']) ? date('d/m/Y H:i', strtotime($critica['creado'])) : '' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <hr class="my-4">

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <h4 class="mb-3">Escribe tu crítica</h4>
                <form action="backend/enviar_critica_serie.php" method="POST">
                    <input type="hidden" name="id_serie" value="<?= (int)$idSerie ?>">

                    <div class="mb-3">
                        <label class="form-label">Tu opinión</label>
                        <textarea name="contenido" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valoración (1-5 ⭐)</label>
                        <select name="puntuacion" class="form-control" required>
                            <option value="">Selecciona una puntuación</option>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button class="btn btn-primary">Enviar crítica</button>
                </form>
            <?php else: ?>
                <p class="text-muted">Debes <a href="login.php">iniciar sesión</a> para escribir una crítica.</p>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php include("footer.php"); ?>

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

    const seasonPanels = document.querySelectorAll(".season-panel-single");
    const seasonSelect = document.getElementById("seasonSelect");

    function showSeason(panelId) {
        seasonPanels.forEach(panel => {
            if (panel.id === panelId) {
                panel.classList.add("active");
            } else {
                panel.classList.remove("active");
            }
        });
    }

    if (seasonSelect) {
        seasonSelect.addEventListener("change", function () {
            showSeason(this.value);
        });
    }
});
</script>

</body>
</html>