<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$noticia = [
    'id' => '',
    'titulo' => '',
    'contenido' => '',
    'imagen' => ''
];

if ($id > 0) {
    $stm = $pdo->prepare("SELECT * FROM noticia WHERE id = ?");
    $stm->execute([$id]);
    $noticia = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$noticia) {
        header("Location: noticias.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id > 0 ? 'Editar noticia' : 'Nueva noticia' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $id > 0 ? 'Editar noticia' : 'Nueva noticia' ?></h1>
        <a href="noticias.php" class="btn btn-secondary">Volver</a>
    </div>

    <form action="noticia_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
        <input type="hidden" name="id" value="<?= htmlspecialchars($noticia['id']) ?>">
        <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($noticia['imagen']) ?>">

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required
                   value="<?= htmlspecialchars($noticia['titulo']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Contenido</label>
            <textarea name="contenido" class="form-control" rows="8" required><?= htmlspecialchars($noticia['contenido']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input type="file" name="imagen_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <?php if (!empty($noticia['imagen'])): ?>
                <div class="mt-3">
                    <p class="mb-2 small text-light">Imagen actual:</p>
                    <img src="../img/noticias/<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen actual" style="max-width: 220px; border-radius: 10px;">
                </div>
            <?php endif; ?>

            <small class="text-secondary d-block mt-2">
                Si no seleccionas una imagen nueva, se mantiene la actual.
            </small>
        </div>

        <button type="submit" class="btn btn-success">
            <?= $id > 0 ? 'Guardar cambios' : 'Crear noticia' ?>
        </button>
    </form>
</div>
</body>
</html>