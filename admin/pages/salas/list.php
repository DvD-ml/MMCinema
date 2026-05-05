<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

$salas = [];
try {
    $salas = $pdo->query("SELECT * FROM sala_config ORDER BY sala ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en salas/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar salas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Administrar salas</h1>
            <p>Gestiona las salas de cine y su configuración.</p>
        </div>
        <a href="form.php" class="btn btn-primary">Añadir sala</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <?php $salaActualizada = ($_GET['ok'] ?? '') === 'updated'; ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">✓</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title"><?= $salaActualizada ? 'Sala modificada' : 'Sala creada' ?></div>
                <div class="admin-alert-message"><?= $salaActualizada ? 'La sala se ha modificado correctamente.' : 'La sala se ha creado correctamente.' ?></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted']) || isset($_GET['borrado'])): ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">✓</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Sala eliminada</div>
                <div class="admin-alert-message">La sala se ha eliminado correctamente.</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="admin-alert-inline error">
            <div class="admin-alert-icon">✕</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Error</div>
                <div class="admin-alert-message">Error al procesar la sala.</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Sala</th>
                    <th>Filas</th>
                    <th>Columnas</th>
                    <th>Capacidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salas as $sala): ?>
                    <tr>
                        <td><?= htmlspecialchars($sala['sala']) ?></td>
                        <td><?= (int)$sala['filas'] ?></td>
                        <td><?= (int)$sala['columnas'] ?></td>
                        <td><?= (int)$sala['filas'] * (int)$sala['columnas'] ?> asientos</td>
                        <td>
                            <div class="admin-actions d-flex gap-2 flex-wrap">
                                <a href="form.php?sala=<?= urlencode($sala['sala']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('¿Eliminar esta sala?')">
                                    <?php require_once __DIR__ . "/../../../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>
                                    <input type="hidden" name="sala" value="<?= htmlspecialchars($sala['sala']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>






