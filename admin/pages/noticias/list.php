<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

$noticias = [];
try {
    $sql = "SELECT * FROM noticia ORDER BY publicado DESC, id DESC";
    $noticias = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en noticias/list.php: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar noticias</title>
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
            <h1>Noticias</h1>
            <p>Publica, edita y elimina noticias de la portada del cine.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="../dashboard/index.php" class="btn btn-outline-light">Panel</a>
            <a href="form.php" class="btn btn-warning">+ Nueva noticia</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <?php $noticiaActualizada = ($_GET['ok'] ?? '') === 'updated'; ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">&#10003;</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title"><?= $noticiaActualizada ? 'Noticia modificada' : 'Noticia creada' ?></div>
                <div class="admin-alert-message"><?= $noticiaActualizada ? 'La noticia se ha modificado correctamente.' : 'La noticia se ha creado correctamente.' ?></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted']) || isset($_GET['borrado'])): ?>
        <div class="admin-alert-inline success">
            <div class="admin-alert-icon">&#10003;</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Noticia eliminada</div>
                <div class="admin-alert-message">La noticia se ha eliminado correctamente.</div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="admin-alert-inline error">
            <div class="admin-alert-icon">&#10005;</div>
            <div class="admin-alert-content">
                <div class="admin-alert-title">Error</div>
                <div class="admin-alert-message">
                    <?= $_GET['error'] === 'imagen' ? 'No se pudo procesar la imagen. Intenta con otro archivo.' : 'No se pudo procesar la noticia.' ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card p-3 p-lg-4">
        <div class="admin-table-wrap">
            <table class="admin-table table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($noticias as $n): ?>
                        <tr>
                            <td><?= (int)$n['id'] ?></td>
                            <td><img class="admin-thumb admin-thumb--wide" src="../../../assets/img/noticias/<?= htmlspecialchars($n['imagen'] ?: 'noticia-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['titulo']) ?>"></td>
                            <td class="text-wrap-cell"><?= htmlspecialchars($n['titulo']) ?></td>
                            <td><?= htmlspecialchars($n['publicado']) ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="form.php?id=<?= (int)$n['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('¿Seguro que quieres borrar esta noticia?')">
                                        <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                                        <?php echo CSRF::campoFormulario(); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Borrar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($noticias)): ?>
                        <tr><td colspan="5" class="text-center">No hay noticias todavía.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>






