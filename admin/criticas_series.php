<?php
require_once "auth.php";
verificarAuth();

session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/auth.php");

$criticas = $pdo->query("
    SELECT 
        cs.*,
        u.username,
        s.titulo AS serie_titulo
    FROM critica_serie cs
    INNER JOIN usuario u ON cs.id_usuario = u.id
    INNER JOIN serie s ON cs.id_serie = s.id
    ORDER BY cs.creado DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Críticas de series | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Críticas de series</h1>
        <a href="series_panel.php" class="btn btn-outline-light">Resumen</a>
    </div>

    <?php mm_render_series_admin_nav('criticas'); ?>

    <div class="admin-table-wrap">
        <table class="admin-table table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Serie</th>
                    <th>Puntuación</th>
                    <th>Contenido</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($criticas as $critica): ?>
                    <tr>
                        <td><?= (int)$critica['id'] ?></td>
                        <td><?= htmlspecialchars($critica['username']) ?></td>
                        <td><?= htmlspecialchars($critica['serie_titulo']) ?></td>
                        <td><?= (int)$critica['puntuacion'] ?>/5</td>
                        <td style="min-width:300px; white-space:normal;"><?= nl2br(htmlspecialchars($critica['contenido'])) ?></td>
                        <td><?= htmlspecialchars($critica['creado']) ?></td>
                        <td>
                            <div class="acciones">
                                <a href="borrar_critica_serie.php?id=<?= (int)$critica['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres borrar esta crítica?');">Borrar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($criticas)): ?>
                    <tr><td colspan="7">No hay críticas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>