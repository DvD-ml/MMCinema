<?php
require_once "auth.php";
require_once "../config/conexion.php";

$salas = $pdo->query("SELECT * FROM sala_config ORDER BY sala ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar salas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Administrar salas</h1>
            <p>Gestiona las salas de cine y su configuración.</p>
        </div>
        <a href="sala_form.php" class="btn btn-primary">Aúadir sala</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Sala guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Error al procesar la sala.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                <a href="sala_form.php?sala=<?= urlencode($sala['sala']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="sala_borrar.php?sala=<?= urlencode($sala['sala']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta sala?')">Eliminar</a>
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
