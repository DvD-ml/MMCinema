<?php
session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . '/includes/series_admin_ui.php');
require_once(__DIR__ . '/auth.php');

$stats = [
    'series' => 0,
    'temporadas' => 0,
    'episodios' => 0,
    'criticas' => 0,
    'destacadas' => 0,
];

$queries = [
    'series' => "SELECT COUNT(*) FROM serie",
    'temporadas' => "SELECT COUNT(*) FROM temporada",
    'episodios' => "SELECT COUNT(*) FROM episodio",
    'criticas' => "SELECT COUNT(*) FROM critica_serie",
    'destacadas' => "SELECT COUNT(*) FROM serie WHERE destacada = 1",
];

foreach ($queries as $key => $sql) {
    $stats[$key] = (int)$pdo->query($sql)->fetchColumn();
}

$series = $pdo->query("
    SELECT id, titulo, estado, destacada, creado
    FROM serie
    ORDER BY creado DESC, id DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

$ultimasCriticas = $pdo->query("
    SELECT 
        cs.puntuacion,
        cs.creado,
        s.titulo AS serie_titulo,
        u.username
    FROM critica_serie cs
    INNER JOIN serie s ON s.id = cs.id_serie
    INNER JOIN usuario u ON u.id = cs.id_usuario
    ORDER BY cs.creado DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen series | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-3">
        <div>
            <h1 class="mb-1">Resumen de series</h1>
            <p class="text-muted mb-0">Desde aquí controlas catálogo, temporadas, episodios y críticas sin ir saltando entre páginas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="agregar_serie.php" class="btn btn-primary">+ Nueva serie</a>
            <a href="agregar_temporada.php" class="btn btn-outline-light">+ Nueva temporada</a>
            <a href="agregar_episodio.php" class="btn btn-outline-light">+ Nuevo episodio</a>
        </div>
    </div>

    <?php mm_render_series_admin_nav('panel'); ?>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="text-secondary small">Series</div>
                    <div class="display-6 fw-bold"><?= $stats['series'] ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="text-secondary small">Temporadas</div>
                    <div class="display-6 fw-bold"><?= $stats['temporadas'] ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="text-secondary small">Episodios</div>
                    <div class="display-6 fw-bold"><?= $stats['episodios'] ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="text-secondary small">Críticas</div>
                    <div class="display-6 fw-bold"><?= $stats['criticas'] ?></div>
                    <div class="small text-warning mt-2">Destacadas: <?= $stats['destacadas'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card bg-dark text-white h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Últimas series</span>
                    <a href="series.php" class="btn btn-sm btn-outline-light">Ver todas</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Destacada</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($series as $serie): ?>
                                <tr>
                                    <td><?= htmlspecialchars($serie['titulo']) ?></td>
                                    <td><?= htmlspecialchars($serie['estado']) ?></td>
                                    <td><?= (int)$serie['destacada'] ? 'Sí' : 'No' ?></td>
                                    <td class="text-end">
                                        <a href="temporadas.php?id_serie=<?= (int)$serie['id'] ?>" class="btn btn-sm btn-outline-light">Abrir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$series): ?>
                                <tr><td colspan="4">Todavía no hay series cargadas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card bg-dark text-white h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Últimas críticas</span>
                    <a href="criticas_series.php" class="btn btn-sm btn-outline-light">Moderar</a>
                </div>

                <div class="card-body">
                    <?php if ($ultimasCriticas): ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($ultimasCriticas as $critica): ?>
                                <div class="border rounded-3 p-3" style="border-color:rgba(255,255,255,.08)!important;">
                                    <div class="d-flex justify-content-between gap-3">
                                        <strong><?= htmlspecialchars($critica['serie_titulo']) ?></strong>
                                        <span class="text-warning"><?= (int)$critica['puntuacion'] ?>/5</span>
                                    </div>
                                    <div class="small text-secondary"><?= htmlspecialchars($critica['username']) ?> · <?= htmlspecialchars($critica['creado']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">Aún no hay críticas de series.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>