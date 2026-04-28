<?php
require_once "auth.php";
require_once("../config/conexion.php");

$stats = [
    'peliculas' => (int)$pdo->query("SELECT COUNT(*) FROM pelicula")->fetchColumn(),
    'proyecciones' => (int)$pdo->query("SELECT COUNT(*) FROM proyeccion")->fetchColumn(),
    'tickets' => (int)$pdo->query("SELECT COUNT(*) FROM ticket")->fetchColumn(),
    'noticias' => (int)$pdo->query("SELECT COUNT(*) FROM noticia")->fetchColumn(),
    'criticas' => (int)$pdo->query("SELECT COUNT(*) FROM critica")->fetchColumn(),
    'usuarios' => (int)$pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn(),
    'series' => (int)$pdo->query("SELECT COUNT(*) FROM serie")->fetchColumn(),
    'temporadas' => (int)$pdo->query("SELECT COUNT(*) FROM temporada")->fetchColumn(),
    'episodios' => (int)$pdo->query("SELECT COUNT(*) FROM episodio")->fetchColumn(),
    'criticas_series' => (int)$pdo->query("SELECT COUNT(*) FROM critica_serie")->fetchColumn(),
];

$ultimasPeliculas = $pdo->query("SELECT id, titulo, poster, fecha_estreno FROM pelicula ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$ultimasNoticias = $pdo->query("SELECT id, titulo, publicado FROM noticia ORDER BY publicado DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$ultimosTickets = $pdo->query("SELECT codigo, total, created_at FROM ticket ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Panel de administración</h1>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="admin-glass-card p-4 h-100">
                <h2 class="h4 mb-3">Accesos rápidos</h2>
                <div class="admin-quick-grid">
                    <a class="admin-quick-link" href="pelicula_form.php"><strong>Aúadir Películas</strong></a>
                    <a class="admin-quick-link" href="agregar_serie.php"><strong>Aúadir Series</strong></a>
                    <a class="admin-quick-link" href="noticia_form.php"><strong>Aúadir Noticias</strong></a>
                    <a class="admin-quick-link" href="usuario_form.php"><strong>Aúadir Usuarios</strong></a>
                    <a class="admin-quick-link" href="criticas.php"><strong>Aúadir Críticas</strong></a>
                    <a class="admin-quick-link" href="../cartelera.php" target="_blank" rel="noopener"><strong>Volver a web</strong></a>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-glass-card p-4 h-100">
                <h2 class="h4 mb-3">Resumen del sistema</h2>
                <div class="d-grid gap-2 small">
                    <div class="d-flex justify-content-between"><span class="text-secondary">Proyecciones</span><strong><?= $stats['proyecciones'] ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Críticas de películas</span><strong><?= $stats['criticas'] ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Series</span><strong><?= $stats['series'] ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Temporadas</span><strong><?= $stats['temporadas'] ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Episodios</span><strong><?= $stats['episodios'] ?></strong></div>
                    <div class="d-flex justify-content-between"><span class="text-secondary">Críticas de series</span><strong><?= $stats['criticas_series'] ?></strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimas películas aúadidas</h2>
                    <a href="peliculas.php" class="btn btn-sm btn-outline-light">Abrir</a>
                </div>
                <?php if ($ultimasPeliculas): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimasPeliculas as $p): ?>
                            <div class="d-flex gap-3 align-items-center">
                                <img class="admin-thumb" src="../assets/img/posters/<?= htmlspecialchars($p['poster']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>">
                                <div>
                                    <strong><?= htmlspecialchars($p['titulo']) ?></strong>
                                    <div class="small text-secondary">Estreno: <?= htmlspecialchars($p['fecha_estreno']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-secondary">Aún no hay películas.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimas noticias aúadidas</h2>
                    <a href="noticias.php" class="btn btn-sm btn-outline-light">Abrir</a>
                </div>
                <?php if ($ultimasNoticias): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimasNoticias as $n): ?>
                            <div>
                                <strong><?= htmlspecialchars($n['titulo']) ?></strong>
                                <div class="small text-secondary"><?= htmlspecialchars($n['publicado']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-secondary">Aún no hay noticias.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimos tickets generados</h2>
                </div>
                <?php if ($ultimosTickets): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimosTickets as $t): ?>
                            <div class="border-bottom pb-2" style="border-color: rgba(255,255,255,.08)!important;">
                                <strong><?= htmlspecialchars($t['codigo']) ?></strong>
                                <div class="small text-secondary"><?= htmlspecialchars($t['created_at']) ?> · <?= number_format((float)$t['total'], 2) ?> EUR</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-secondary">Todavía no hay tickets.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
