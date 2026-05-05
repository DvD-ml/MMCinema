<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../config/conexion.php";

$tabActiva = $_GET['tab'] ?? 'peliculas';
if (!in_array($tabActiva, ['peliculas', 'series'], true)) {
    $tabActiva = 'peliculas';
}

$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$por_pagina = 8;

/* Películas */
$pagina_pelis = isset($_GET['pp']) && is_numeric($_GET['pp']) ? max(1, (int)$_GET['pp']) : 1;
$offset_pelis = ($pagina_pelis - 1) * $por_pagina;

if (!empty($busqueda)) {
    $sqlCountPelis = "
        SELECT COUNT(*) FROM critica c
        LEFT JOIN pelicula p ON c.id_pelicula = p.id
        WHERE p.titulo LIKE ?
    ";
    $stmtCount = $pdo->prepare($sqlCountPelis);
    $stmtCount->execute(["%$busqueda%"]);
    $total_criticas_pelis = (int)$stmtCount->fetchColumn();
    
    $sqlPelis = "
        SELECT c.*, u.username, p.titulo, p.poster, p.id as pelicula_id
        FROM critica c
        LEFT JOIN usuario u ON c.id_usuario = u.id
        LEFT JOIN pelicula p ON c.id_pelicula = p.id
        WHERE p.titulo LIKE ?
        ORDER BY c.creado DESC
        LIMIT $por_pagina OFFSET $offset_pelis
    ";
    $stmtPelis = $pdo->prepare($sqlPelis);
    $stmtPelis->execute(["%$busqueda%"]);
} else {
    $total_criticas_pelis = (int)$pdo->query("SELECT COUNT(*) FROM critica")->fetchColumn();
    $stmtPelis = $pdo->query("
        SELECT c.*, u.username, p.titulo, p.poster, p.id as pelicula_id
        FROM critica c
        LEFT JOIN usuario u ON c.id_usuario = u.id
        LEFT JOIN pelicula p ON c.id_pelicula = p.id
        ORDER BY c.creado DESC
        LIMIT $por_pagina OFFSET $offset_pelis
    ");
}

$total_paginas_pelis = max(1, (int)ceil($total_criticas_pelis / $por_pagina));
$criticasPeliculas = $stmtPelis->fetchAll(PDO::FETCH_ASSOC);

/* Series */
$pagina_series = isset($_GET['ps']) && is_numeric($_GET['ps']) ? max(1, (int)$_GET['ps']) : 1;
$offset_series = ($pagina_series - 1) * $por_pagina;

if (!empty($busqueda)) {
    $sqlCountSeries = "
        SELECT COUNT(*) FROM critica_serie cs
        LEFT JOIN serie s ON cs.id_serie = s.id
        WHERE s.titulo LIKE ?
    ";
    $stmtCount = $pdo->prepare($sqlCountSeries);
    $stmtCount->execute(["%$busqueda%"]);
    $total_criticas_series = (int)$stmtCount->fetchColumn();
    
    $sqlSeries = "
        SELECT cs.*, u.username, s.titulo, s.poster, s.id as serie_id
        FROM critica_serie cs
        LEFT JOIN usuario u ON cs.id_usuario = u.id
        LEFT JOIN serie s ON cs.id_serie = s.id
        WHERE s.titulo LIKE ?
        ORDER BY cs.creado DESC
        LIMIT $por_pagina OFFSET $offset_series
    ";
    $stmtSeries = $pdo->prepare($sqlSeries);
    $stmtSeries->execute(["%$busqueda%"]);
} else {
    $total_criticas_series = (int)$pdo->query("SELECT COUNT(*) FROM critica_serie")->fetchColumn();
    $stmtSeries = $pdo->query("
        SELECT cs.*, u.username, s.titulo, s.poster, s.id as serie_id
        FROM critica_serie cs
        LEFT JOIN usuario u ON cs.id_usuario = u.id
        LEFT JOIN serie s ON cs.id_serie = s.id
        ORDER BY cs.creado DESC
        LIMIT $por_pagina OFFSET $offset_series
    ");
}

$total_paginas_series = max(1, (int)ceil($total_criticas_series / $por_pagina));
$criticasSeries = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Críticas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/criticas.css">
</head>
<body>
<?php include "../components/navbar.php"; ?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Críticas de la comunidad</h1>
        <p>Lee opiniones sobre películas y series de MMCINEMA</p>
    </div>

    <div class="criticas-tabs-wrap mb-3">
        <div class="criticas-tabs">
            <button class="criticas-tab-btn <?= $tabActiva === 'peliculas' ? 'active' : '' ?>" type="button" data-tab="peliculas">
                Críticas de películas
            </button>
            <button class="criticas-tab-btn <?= $tabActiva === 'series' ? 'active' : '' ?>" type="button" data-tab="series">
                Críticas de series
            </button>
        </div>
    </div>

    <!-- Buscador Simple -->
    <form method="GET" class="criticas-search-simple mb-4">
        <input type="hidden" name="tab" value="<?= htmlspecialchars($tabActiva) ?>">
        <div class="search-input-wrap">
            <input 
                type="text" 
                name="buscar" 
                class="search-input" 
                placeholder="<?= $tabActiva === 'peliculas' ? 'Buscar película...' : 'Buscar serie...' ?>"
                value="<?= htmlspecialchars($busqueda) ?>"
                autocomplete="off">
            <button class="search-btn" type="submit" title="Buscar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
            <?php if (!empty($busqueda)): ?>
                <a href="?tab=<?= htmlspecialchars($tabActiva) ?>" class="search-clear">✕</a>
            <?php endif; ?>
        </div>
    </form>

    <section id="tab-peliculas" class="criticas-tab-panel <?= $tabActiva === 'peliculas' ? 'active' : '' ?>">
        <div class="mb-4">
            <h2 class="mb-1">Críticas de películas</h2>
            <p class="text-muted mb-0">Opiniones enviadas desde la cartelera y fichas de películas.</p>
            <?php if (!empty($busqueda)): ?>
                <p class="text-muted small mt-2">Resultados para: <strong><?= htmlspecialchars($busqueda) ?></strong> (<?= $total_criticas_pelis ?> crítica<?= $total_criticas_pelis !== 1 ? 's' : '' ?>)</p>
            <?php endif; ?>
        </div>

        <?php if (empty($criticasPeliculas)): ?>
            <div class="alert alert-info text-center">
                <?= !empty($busqueda) ? 'No se encontraron críticas para tu búsqueda.' : 'Todavía no hay críticas de películas.' ?>
            </div>
        <?php else: ?>
            <?php foreach ($criticasPeliculas as $c): ?>
                <div class="critica-card mb-4">
                    <div class="row g-3">
                        <!-- Poster -->
                        <div class="col-auto">
                            <?php if (!empty($c['poster'])): ?>
                                <div class="critica-poster-wrap">
                                    <img src="../assets/img/posters/<?= htmlspecialchars($c['poster']) ?>" 
                                         alt="<?= htmlspecialchars($c['titulo']) ?>"
                                         class="critica-poster">
                                </div>
                            <?php else: ?>
                                <div class="critica-poster-wrap critica-poster-placeholder">
                                    <div class="placeholder-content">
                                        <span>Sin poster</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Contenido -->
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <strong><?= htmlspecialchars($c['username'] ?: 'Anónimo') ?></strong>
                                    <div class="small text-muted">
                                        <?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?>
                                    </div>
                                </div>
                                <?php if (!empty($c['puntuacion'])): ?>
                                    <div class="critica-stars-display">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?= $i <= (int)$c['puntuacion'] ? 'on' : 'off' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p class="critica-obra mb-2">
                                <strong>Película:</strong> 
                                <?php if (!empty($c['pelicula_id'])): ?>
                                    <a href="pelicula.php?id=<?= (int)$c['pelicula_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?>
                                <?php endif; ?>
                            </p>
                            <p class="mb-0 critica-contenido"><?= nl2br(htmlspecialchars($c['contenido'] ?? $c['texto'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <nav aria-label="Paginación críticas películas" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagina_pelis <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=peliculas&pp=<?= $pagina_pelis - 1 ?>&ps=<?= $pagina_series ?>&buscar=<?= urlencode($busqueda) ?>">&lsaquo;</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_paginas_pelis; $i++): ?>
                        <li class="page-item <?= $pagina_pelis == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=peliculas&pp=<?= $i ?>&ps=<?= $pagina_series ?>&buscar=<?= urlencode($busqueda) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina_pelis >= $total_paginas_pelis ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=peliculas&pp=<?= $pagina_pelis + 1 ?>&ps=<?= $pagina_series ?>&buscar=<?= urlencode($busqueda) ?>">&rsaquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </section>

    <section id="tab-series" class="criticas-tab-panel <?= $tabActiva === 'series' ? 'active' : '' ?>">
        <div class="mb-4">
            <h2 class="mb-1">Críticas de series</h2>
            <p class="text-muted mb-0">Opiniones enviadas desde las fichas de series.</p>
            <?php if (!empty($busqueda)): ?>
                <p class="text-muted small mt-2">Resultados para: <strong><?= htmlspecialchars($busqueda) ?></strong> (<?= $total_criticas_series ?> crítica<?= $total_criticas_series !== 1 ? 's' : '' ?>)</p>
            <?php endif; ?>
        </div>

        <?php if (empty($criticasSeries)): ?>
            <div class="alert alert-info text-center">
                <?= !empty($busqueda) ? 'No se encontraron críticas para tu búsqueda.' : 'Todavía no hay críticas de series.' ?>
            </div>
        <?php else: ?>
            <?php foreach ($criticasSeries as $c): ?>
                <div class="critica-card mb-4">
                    <div class="row g-3">
                        <!-- Poster -->
                        <div class="col-auto">
                            <?php if (!empty($c['poster'])): ?>
                                <div class="critica-poster-wrap">
                                    <img src="../<?= htmlspecialchars($c['poster']) ?>" 
                                         alt="<?= htmlspecialchars($c['titulo']) ?>"
                                         class="critica-poster">
                                </div>
                            <?php else: ?>
                                <div class="critica-poster-wrap critica-poster-placeholder">
                                    <div class="placeholder-content">
                                        <span>Sin poster</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Contenido -->
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <strong><?= htmlspecialchars($c['username'] ?: 'Anónimo') ?></strong>
                                    <div class="small text-muted">
                                        <?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?>
                                    </div>
                                </div>
                                <?php if (!empty($c['puntuacion'])): ?>
                                    <div class="critica-stars-display">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?= $i <= (int)$c['puntuacion'] ? 'on' : 'off' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <p class="critica-obra mb-2">
                                <strong>Serie:</strong> 
                                <?php if (!empty($c['serie_id'])): ?>
                                    <a href="serie.php?id=<?= (int)$c['serie_id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?>
                                <?php endif; ?>
                            </p>
                            <p class="mb-0 critica-contenido"><?= nl2br(htmlspecialchars($c['contenido'] ?? '')) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <nav aria-label="Paginación críticas series" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagina_series <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $pagina_series - 1 ?>&buscar=<?= urlencode($busqueda) ?>">&lsaquo;</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_paginas_series; $i++): ?>
                        <li class="page-item <?= $pagina_series == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina_series >= $total_paginas_series ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $pagina_series + 1 ?>&buscar=<?= urlencode($busqueda) ?>">&rsaquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </section>
</main>

<?php include "../components/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botones = document.querySelectorAll('.criticas-tab-btn');
    const paneles = document.querySelectorAll('.criticas-tab-panel');

    botones.forEach(boton => {
        boton.addEventListener('click', function () {
            const tab = this.dataset.tab;
            
            // Redirigir a la nueva tab (recarga la página)
            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            url.searchParams.delete('buscar'); // Limpiar búsqueda al cambiar tab
            url.searchParams.set('pp', 1); // Volver a página 1
            url.searchParams.set('ps', 1); // Volver a página 1
            window.location.href = url.toString();
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>