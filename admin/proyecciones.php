<?php
require_once "auth.php";
require_once "../config/conexion.php";

$sql = "
    SELECT 
        pr.id,
        pr.id_pelicula,
        pr.fecha,
        pr.hora,
        pr.sala,
        p.titulo,
        COUNT(DISTINCT ta.id) as asientos_vendidos,
        sc.filas * sc.columnas as capacidad_total
    FROM proyeccion pr
    JOIN pelicula p ON pr.id_pelicula = p.id
    JOIN sala_config sc ON pr.sala = sc.sala
    LEFT JOIN ticket_asiento ta ON pr.id = ta.id_proyeccion
    GROUP BY pr.id
    ORDER BY pr.fecha DESC, pr.hora DESC
";

$proyecciones = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar proyecciones</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Administrar proyecciones</h1>
            <p>Gestiona las proyecciones de películas, horarios y salas.</p>
        </div>
        <a href="proyeccion_form.php" class="btn btn-primary">Aúadir proyección</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Proyección guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Error al procesar la proyección.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card">
        <table class="table table-dark table-hover">
            <thead>
                <tr>
                    <th>Película</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Sala</th>
                    <th>Ocupación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proyecciones as $proy): ?>
                    <tr>
                        <td><?= htmlspecialchars($proy['titulo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($proy['fecha'])) ?></td>
                        <td><?= substr($proy['hora'], 0, 5) ?></td>
                        <td><?= htmlspecialchars($proy['sala']) ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <?php 
                                    $porcentaje = $proy['capacidad_total'] > 0 
                                        ? round(($proy['asientos_vendidos'] / $proy['capacidad_total']) * 100) 
                                        : 0;
                                    $clase = $porcentaje >= 80 ? 'bg-danger' : ($porcentaje >= 50 ? 'bg-warning' : 'bg-success');
                                ?>
                                <div class="progress-bar <?= $clase ?>" style="width: <?= $porcentaje ?>%">
                                    <?= $proy['asientos_vendidos'] ?>/<?= $proy['capacidad_total'] ?> (<?= $porcentaje ?>%)
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="admin-actions d-flex gap-2 flex-wrap">
                                <a href="proyeccion_form.php?id=<?= (int)$proy['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="proyeccion_borrar.php?id=<?= (int)$proy['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta proyección?')">Eliminar</a>
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
