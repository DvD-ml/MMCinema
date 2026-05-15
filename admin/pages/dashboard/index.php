<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

$stats = [
    'peliculas' => 0,
    'proyecciones' => 0,
    'tickets' => 0,
    'noticias' => 0,
    'criticas' => 0,
    'usuarios' => 0,
    'series' => 0,
    'temporadas' => 0,
    'episodios' => 0,
    'criticas_series' => 0,
];

$ultimasPeliculas = [];
$ultimasNoticias = [];
$ultimosTickets = [];

try {
    $stats['peliculas'] = (int)$pdo->query("SELECT COUNT(*) FROM pelicula")->fetchColumn();
    $stats['proyecciones'] = (int)$pdo->query("SELECT COUNT(*) FROM proyeccion")->fetchColumn();
    $stats['tickets'] = (int)$pdo->query("SELECT COUNT(*) FROM ticket")->fetchColumn();
    $stats['noticias'] = (int)$pdo->query("SELECT COUNT(*) FROM noticia")->fetchColumn();
    $stats['criticas'] = (int)$pdo->query("SELECT COUNT(*) FROM critica")->fetchColumn();
    $stats['usuarios'] = (int)$pdo->query("SELECT COUNT(*) FROM usuario")->fetchColumn();
    $stats['series'] = (int)$pdo->query("SELECT COUNT(*) FROM serie")->fetchColumn();
    $stats['temporadas'] = (int)$pdo->query("SELECT COUNT(*) FROM temporada")->fetchColumn();
    $stats['episodios'] = (int)$pdo->query("SELECT COUNT(*) FROM episodio")->fetchColumn();
    $stats['criticas_series'] = (int)$pdo->query("SELECT COUNT(*) FROM critica_serie")->fetchColumn();

    $ultimasPeliculas = $pdo->query("SELECT id, titulo, poster, fecha_estreno FROM pelicula ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $ultimasNoticias = $pdo->query("SELECT id, titulo, publicado FROM noticia ORDER BY publicado DESC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $ultimosTickets = $pdo->query("SELECT codigo, total, created_at FROM ticket ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Dashboard query error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Panel de administración</h1>
        </div>
    </div>

    

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimas películas</h2>
                    <a href="../peliculas/list.php" class="btn btn-sm btn-outline-light">Abrir</a>
                </div>
                <?php if ($ultimasPeliculas): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimasPeliculas as $p): ?>
                            <div class="d-flex gap-3 align-items-center">
                                <img class="admin-thumb" src="../../../assets/img/posters/<?= htmlspecialchars($p['poster']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>">
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
                    <h2 class="h5 mb-0">Últimas noticias</h2>
                    <a href="../noticias/list.php" class="btn btn-sm btn-outline-light">Abrir</a>
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
                <h2 class="h5 mb-3">Últimos tickets</h2>
                <?php if ($ultimosTickets): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimosTickets as $t): ?>
                            <div class="border-bottom pb-2" style="border-color: rgba(255,255,255,.08)!important;">
                                <strong><?= htmlspecialchars($t['codigo']) ?></strong>
                                <div class="small text-secondary"><?= htmlspecialchars($t['created_at']) ?> - <?= number_format((float)$t['total'], 2) ?> EUR</div>
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
