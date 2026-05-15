<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

$stats = [
    'series' => 0,
    'temporadas' => 0,
    'episodios' => 0,
    'criticas' => 0,
    'destacadas' => 0,
];

$series = [];
$ultimasCriticas = [];

try {
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
} catch (Exception $e) {
    error_log("Error en series/panel.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen series | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
        <div>
            <h1 class="mb-1">Resumen de series</h1>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= htmlspecialchars(mm_admin_url('series/form.php')) ?>" class="btn btn-primary">Nueva serie</a>
            <a href="<?= htmlspecialchars(mm_admin_url('series/list.php')) ?>" class="btn btn-outline-light">Ver todas</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimas series Añadidas</h2>
                    <a href="<?= htmlspecialchars(mm_admin_url('series/list.php')) ?>" class="btn btn-sm btn-outline-light">Ver todas</a>
                </div>

                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Destacada</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($series as $serie): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($serie['titulo']) ?></strong></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($serie['estado']) ?></span></td>
                                    <td><?= (int)$serie['destacada'] ? 'Sí' : 'No' ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/list.php') . '?id_serie=' . (int)$serie['id']) ?>" class="btn btn-sm btn-outline-light">Temporadas</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$series): ?>
                                <tr><td colspan="4" class="text-center text-secondary py-4">Todavía no hay series cargadas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="admin-glass-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 mb-0">Últimas críticas</h2>
                    <a href="<?= htmlspecialchars(mm_admin_url('series/criticas/list.php')) ?>" class="btn btn-sm btn-outline-light">Moderar</a>
                </div>

                <?php if ($ultimasCriticas): ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($ultimasCriticas as $critica): ?>
                            <div class="admin-critica-card">
                                <div class="d-flex justify-content-between gap-3 mb-2">
                                    <strong><?= htmlspecialchars($critica['serie_titulo']) ?></strong>
                                    <span class="admin-rating"><?= (int)$critica['puntuacion'] ?>/5</span>
                                </div>
                                <div class="small text-secondary"><?= htmlspecialchars($critica['username']) ?> - <?= htmlspecialchars($critica['creado']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-secondary text-center py-4">Aún no hay críticas de series.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
