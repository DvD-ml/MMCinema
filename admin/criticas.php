<?php
require_once "auth.php";
require_once "../config/conexion.php";

$criticasPeliculas = $pdo->query("
    SELECT c.*, u.username, u.email, p.titulo
    FROM critica c
    LEFT JOIN usuario u ON c.id_usuario = u.id
    LEFT JOIN pelicula p ON c.id_pelicula = p.id
    ORDER BY c.creado DESC, c.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$criticasSeries = $pdo->query("
    SELECT cs.*, u.username, u.email, s.titulo
    FROM critica_serie cs
    LEFT JOIN usuario u ON cs.id_usuario = u.id
    LEFT JOIN serie s ON cs.id_serie = s.id
    ORDER BY cs.creado DESC, cs.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar críticas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Críticas de usuarios</h1>
            <p>Desde aquí puedes crear, editar y borrar críticas de películas y series.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="critica_form.php?tipo=pelicula" class="btn btn-primary">Añadir crítica de película</a>
            <a href="critica_form.php?tipo=serie" class="btn btn-warning">Añadir crítica de serie</a>
            <a href="index.php" class="btn btn-outline-light">Volver al panel</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success">Crítica guardada correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">No se pudo guardar la crítica. Revisa todos los datos.</div>
    <?php endif; ?>

    <div class="admin-glass-card p-3 p-lg-4 mb-4">
        <h2 class="h4 mb-3">Críticas de películas</h2>
        <div class="admin-table-wrap">
            <table class="admin-table table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Película</th>
                        <th>Puntuación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($criticasPeliculas as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['username'] ?? 'Usuario eliminado') ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['titulo'] ?? 'Película eliminada') ?></td>
                        <td><?= !empty($c['puntuacion']) ? (int)$c['puntuacion'] . '/5' : '-' ?></td>
                        <td class="text-wrap-cell"><?= nl2br(htmlspecialchars($c['contenido'] ?? '')) ?></td>
                        <td><?= !empty($c['creado']) ? htmlspecialchars($c['creado']) : '-' ?></td>
                        <td>
                            <div class="admin-actions d-flex gap-2 flex-wrap">
                                <a href="critica_form.php?tipo=pelicula&id=<?= (int)$c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="critica_borrar.php?id=<?= (int)$c['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Seguro que quieres borrar esta crítica de película?')">
                                    Borrar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($criticasPeliculas)): ?>
                    <tr><td colspan="8" class="text-center">No hay críticas de películas registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-glass-card p-3 p-lg-4">
        <h2 class="h4 mb-3">Críticas de series</h2>
        <div class="admin-table-wrap">
            <table class="admin-table table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Serie</th>
                        <th>Puntuación</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($criticasSeries as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['username'] ?? 'Usuario eliminado') ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['titulo'] ?? 'Serie eliminada') ?></td>
                        <td><?= !empty($c['puntuacion']) ? (int)$c['puntuacion'] . '/5' : '-' ?></td>
                        <td class="text-wrap-cell"><?= nl2br(htmlspecialchars($c['contenido'] ?? '')) ?></td>
                        <td><?= !empty($c['creado']) ? htmlspecialchars($c['creado']) : '-' ?></td>
                        <td>
                            <div class="admin-actions d-flex gap-2 flex-wrap">
                                <a href="critica_form.php?tipo=serie&id=<?= (int)$c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="borrar_critica_serie.php?id=<?= (int)$c['id'] ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Seguro que quieres borrar esta crítica de serie?')">
                                    Borrar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($criticasSeries)): ?>
                    <tr><td colspan="8" class="text-center">No hay críticas de series registradas.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>