<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

$peliculas = [];
try {
    $sql = "SELECT p.*, g.nombre AS genero FROM pelicula p LEFT JOIN genero g ON p.id_genero = g.id ORDER BY p.id DESC";
    $peliculas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en peliculas/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar películas</title>
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
            <h1>Películas</h1>
            <p>Gestiona títulos, pósters, género, fecha de estreno y trailer.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="../dashboard/index.php" class="btn btn-outline-light">Panel</a>
            <a href="form.php" class="btn btn-warning">+ Nueva película</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">?</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Película guardada</div>
                <div class="admin-alert-message">La película se ha guardado correctamente.</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">?</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Película eliminada</div>
                <div class="admin-alert-message">La película se ha eliminado correctamente.</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="admin-alert-inline error">
            <div class="admin-alert-icon">?</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Error</div>
                <div class="admin-alert-message">Ha ocurrido un error al procesar la película.</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'imagen'): ?>
        <div class="admin-alert-inline warning">
            <div class="admin-alert-icon">?</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Error en la imagen</div>
                <div class="admin-alert-message">No se pudo procesar la imagen. Intenta con otro archivo.</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card p-3 p-lg-4">
        <div class="admin-table-wrap">
            <table class="admin-table table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>Estreno</th>
                        <th>Duración</th>
                        <th>Edad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($peliculas as $p): ?>
                        <tr>
                            <td><?= (int)$p['id'] ?></td>
                            <td><img class="admin-thumb" src="../../../assets/img/posters/<?= htmlspecialchars($p['poster'] ?: 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['titulo']) ?>"></td>
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['genero'] ?? 'Sin género') ?></td>
                            <td><?= htmlspecialchars($p['fecha_estreno']) ?></td>
                            <td><?= (int)$p['duracion'] ?> min</td>
                            <td><?= htmlspecialchars($p['edad']) ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="form.php?id=<?= (int)$p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres borrar esta película?')">
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <?php echo CSRF::campoFormulario(); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($peliculas)): ?>
                        <tr><td colspan="8" class="text-center">No hay películas todavía.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>






