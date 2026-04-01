<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

/* Películas */
$por_pagina_pelis = 8;
$pagina_pelis = isset($_GET['pp']) && is_numeric($_GET['pp']) ? max(1, (int)$_GET['pp']) : 1;
$offset_pelis = ($pagina_pelis - 1) * $por_pagina_pelis;

$total_criticas_pelis = (int)$pdo->query("SELECT COUNT(*) FROM critica")->fetchColumn();
$total_paginas_pelis = max(1, (int)ceil($total_criticas_pelis / $por_pagina_pelis));

$stmtPelis = $pdo->query("
    SELECT c.*, u.username, p.titulo
    FROM critica c
    LEFT JOIN usuario u ON c.id_usuario = u.id
    LEFT JOIN pelicula p ON c.id_pelicula = p.id
    ORDER BY c.creado DESC
    LIMIT $por_pagina_pelis OFFSET $offset_pelis
");
$criticasPeliculas = $stmtPelis->fetchAll(PDO::FETCH_ASSOC);

/* Series */
$por_pagina_series = 8;
$pagina_series = isset($_GET['ps']) && is_numeric($_GET['ps']) ? max(1, (int)$_GET['ps']) : 1;
$offset_series = ($pagina_series - 1) * $por_pagina_series;

$total_criticas_series = (int)$pdo->query("SELECT COUNT(*) FROM critica_serie")->fetchColumn();
$total_paginas_series = max(1, (int)ceil($total_criticas_series / $por_pagina_series));

$stmtSeries = $pdo->query("
    SELECT cs.*, u.username, s.titulo
    FROM critica_serie cs
    LEFT JOIN usuario u ON cs.id_usuario = u.id
    LEFT JOIN serie s ON cs.id_serie = s.id
    ORDER BY cs.creado DESC
    LIMIT $por_pagina_series OFFSET $offset_series
");
$criticasSeries = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);

$tabActiva = $_GET['tab'] ?? 'peliculas';
if (!in_array($tabActiva, ['peliculas', 'series'], true)) {
    $tabActiva = 'peliculas';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Críticas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include "navbar.php"; ?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Críticas de la comunidad</h1>
        <p>Lee opiniones sobre películas y series de MMCINEMA</p>
    </div>

    <div class="criticas-tabs-wrap mb-4">
        <div class="criticas-tabs">
            <button class="criticas-tab-btn <?= $tabActiva === 'peliculas' ? 'active' : '' ?>" type="button" data-tab="peliculas">
                Críticas de películas
            </button>
            <button class="criticas-tab-btn <?= $tabActiva === 'series' ? 'active' : '' ?>" type="button" data-tab="series">
                Críticas de series
            </button>
        </div>
    </div>

    <section id="tab-peliculas" class="criticas-tab-panel <?= $tabActiva === 'peliculas' ? 'active' : '' ?>">
        <div class="mb-4">
            <h2 class="mb-1">Críticas de películas</h2>
            <p class="text-muted mb-0">Opiniones enviadas desde la cartelera y fichas de películas.</p>
        </div>

        <?php if (empty($criticasPeliculas)): ?>
            <div class="alert alert-info text-center">Todavía no hay críticas de películas.</div>
        <?php else: ?>
            <?php foreach ($criticasPeliculas as $c): ?>
                <div class="critica-card mb-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <strong><?= htmlspecialchars($c['username'] ?: 'Anónimo') ?></strong>
                            <div class="small text-muted">
                                <?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?>
                            </div>
                        </div>
                        <?php if (!empty($c['puntuacion'])): ?>
                            <span class="badge bg-warning text-dark"><?= (int)$c['puntuacion'] ?>/5</span>
                        <?php endif; ?>
                    </div>

                    <p class="critica-obra mb-2">Película: <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?></p>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($c['contenido'] ?? $c['texto'] ?? '')) ?></p>
                </div>
            <?php endforeach; ?>

            <nav aria-label="Paginación críticas películas" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagina_pelis <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=peliculas&pp=<?= $pagina_pelis - 1 ?>&ps=<?= $pagina_series ?>">Anterior</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_paginas_pelis; $i++): ?>
                        <li class="page-item <?= $pagina_pelis == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=peliculas&pp=<?= $i ?>&ps=<?= $pagina_series ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina_pelis >= $total_paginas_pelis ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=peliculas&pp=<?= $pagina_pelis + 1 ?>&ps=<?= $pagina_series ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </section>

    <section id="tab-series" class="criticas-tab-panel <?= $tabActiva === 'series' ? 'active' : '' ?>">
        <div class="mb-4">
            <h2 class="mb-1">Críticas de series</h2>
            <p class="text-muted mb-0">Opiniones enviadas desde las fichas de series.</p>
        </div>

        <?php if (empty($criticasSeries)): ?>
            <div class="alert alert-info text-center">Todavía no hay críticas de series.</div>
        <?php else: ?>
            <?php foreach ($criticasSeries as $c): ?>
                <div class="critica-card mb-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <strong><?= htmlspecialchars($c['username'] ?: 'Anónimo') ?></strong>
                            <div class="small text-muted">
                                <?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?>
                            </div>
                        </div>
                        <?php if (!empty($c['puntuacion'])): ?>
                            <span class="badge bg-warning text-dark"><?= (int)$c['puntuacion'] ?>/5</span>
                        <?php endif; ?>
                    </div>

                    <p class="critica-obra mb-2">Serie: <?= htmlspecialchars($c['titulo'] ?: 'Desconocida') ?></p>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($c['contenido'] ?? '')) ?></p>
                </div>
            <?php endforeach; ?>

            <nav aria-label="Paginación críticas series" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $pagina_series <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $pagina_series - 1 ?>">Anterior</a>
                    </li>

                    <?php for ($i = 1; $i <= $total_paginas_series; $i++): ?>
                        <li class="page-item <?= $pagina_series == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $pagina_series >= $total_paginas_series ? 'disabled' : '' ?>">
                        <a class="page-link" href="?tab=series&pp=<?= $pagina_pelis ?>&ps=<?= $pagina_series + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </section>
</main>

<?php include "footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const botones = document.querySelectorAll('.criticas-tab-btn');
    const paneles = document.querySelectorAll('.criticas-tab-panel');

    botones.forEach(boton => {
        boton.addEventListener('click', function () {
            const tab = this.dataset.tab;

            botones.forEach(b => b.classList.remove('active'));
            paneles.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>