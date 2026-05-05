<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Obtener ID del registro
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

// Inicializar datos
$data = [
    'id' => '',
    'titulo' => '',
    'contenido' => '',
    'imagen' => ''
];

// Si es edición, obtener datos
if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT * FROM noticia WHERE id = ?");
    $stm->execute([$id]);
    $data = $stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        header("Location: list.php");
        exit();
    }
}

// Generar token CSRF
$csrfToken = CSRF::generarToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar noticia' : 'Nueva noticia' ?></title>
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
            <h1><?= $modoEdicion ? 'Editar noticia' : 'Nueva noticia' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la noticia.' : 'Crea una nueva noticia.' ?></p>
        </div>
        <a href="list.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
            <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($data['imagen'] ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required
                       value="<?= htmlspecialchars($data['titulo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Contenido</label>
                <textarea name="contenido" class="form-control" rows="8" required><?= htmlspecialchars($data['contenido'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input type="file" name="imagen_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                <?php if (!empty($data['imagen'])): ?>
                    <div class="mt-3">
                        <p class="mb-2 small text-light">Imagen actual:</p>
                        <img src="../../../assets/img/noticias/<?= htmlspecialchars($data['imagen']) ?>" alt="Imagen actual" style="max-width: 220px; border-radius: 10px;">
                    </div>
                <?php endif; ?>

                <small class="text-secondary d-block mt-2">
                    Si no seleccionas una imagen nueva, se mantiene la actual.
                </small>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear noticia' ?>
                </button>
                <a href="list.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





