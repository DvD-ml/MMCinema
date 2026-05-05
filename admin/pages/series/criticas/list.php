<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

$criticas = [];
try {
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
} catch (PDOException $e) {
    error_log("Error en series/criticas/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Críticas de series | Admin MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../../favicon.svg">
    <link rel="stylesheet" href="../../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/../../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Críticas de series</h1>
        </div>
        <div>
            <a href="<?= htmlspecialchars(mm_admin_url('criticas/form.php') . '?tipo=serie') ?>" class="btn btn-primary">+ Añadir crítica</a>
            <a href="<?= htmlspecialchars(mm_admin_url('series/panel.php')) ?>" class="btn btn-outline-light">Resumen</a>
        </div>
    </div>

    <div class="admin-glass-card p-3 p-lg-4">    <div class="admin-glass-card p-3 p-lg-4">
        <div class="admin-table-wrap">
            <table class="admin-table table align-middle mb-0">
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
                                    <a href="<?= htmlspecialchars(mm_admin_url('criticas/form.php') . '?id=' . (int)$critica['id'] . '&tipo=serie') ?>" class="btn btn-sm btn-primary">Editar</a>
                                    <form method="POST" action="<?= htmlspecialchars(mm_admin_url('criticas/delete.php')) ?>" style="display:inline;">
                                        <?php echo CSRF::campoFormulario(); ?>
                                        <input type="hidden" name="id" value="<?= (int)$critica['id'] ?>">
                                        <input type="hidden" name="tipo" value="serie">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres borrar esta crítica?');">Borrar</button>
                                    </form>
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
</div>
</body>
</html>





