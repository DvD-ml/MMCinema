<?php
require_once "auth.php";
require_once "../config/conexion.php";

$sql = "SELECT * FROM noticia ORDER BY publicado DESC, id DESC";
$noticias = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar noticias</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>
<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Noticias</h1>
            <p>Publica, edita y elimina noticias de la portada del cine.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php" class="btn btn-outline-light">Panel</a>
            <a href="noticia_form.php" class="btn btn-warning">+ Nueva noticia</a>
        </div>
    </div>

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
                            <td><img class="admin-thumb admin-thumb--wide" src="../img/noticias/<?= htmlspecialchars($n['imagen'] ?: 'noticia-placeholder.jpg') ?>" alt="<?= htmlspecialchars($n['titulo']) ?>"></td>
                            <td class="text-wrap-cell"><?= htmlspecialchars($n['titulo']) ?></td>
                            <td><?= htmlspecialchars($n['publicado']) ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="noticia_form.php?id=<?= (int)$n['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="noticia_borrar.php?id=<?= (int)$n['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres borrar esta noticia?')">Borrar</a>
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
