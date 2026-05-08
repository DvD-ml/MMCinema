<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/series_admin_ui.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

// Inicializar datos
$temporada = [
    'id' => '',
    'id_serie' => '',
    'numero_temporada' => '',
    'titulo' => '',
    'descripcion' => '',
    'poster' => '',
    'fecha_estreno' => ''
];

// Si es edición, obtener datos
if ($modoEdicion) {
    $stmt = $pdo->prepare("SELECT * FROM temporada WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $temporada = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$temporada) {
        header("Location: list.php");
        exit();
    }
}

// Obtener serie preseleccionada (si viene en GET)
$idSeriePre = isset($_GET['id_serie']) ? (int)$_GET['id_serie'] : (int)($temporada['id_serie'] ?? 0);

// Obtener opciones
$series = $pdo->query("SELECT id, titulo FROM serie ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);

// Generar token CSRF
$csrfToken = CSRF::generarToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar temporada' : 'Añadir temporada' ?></title>
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
            <h1><?= $modoEdicion ? 'Editar temporada' : 'Añadir temporada' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la temporada.' : 'Crea una nueva temporada.' ?></p>
        </div>
        <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/list.php') . ($idSeriePre > 0 ? '?id_serie=' . $idSeriePre : '')) ?>" class="btn btn-outline-light">Volver</a>
    </div>

    <?php mm_render_series_admin_nav('temporadas', ['id_serie' => $idSeriePre]); ?>

    <?php if (($_GET['error'] ?? '') === 'imagen'): ?>
        <div class="alert alert-danger">
            No se pudo guardar la imagen. Revisa permisos de la carpeta de imagenes y que PHP tenga GD con soporte WebP.
        </div>
    <?php endif; ?>

    <div class="admin-glass-card p-4">
        <form method="POST" action="<?= htmlspecialchars(mm_admin_url('series/temporadas/save.php')) ?>" enctype="multipart/form-data">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($temporada['id'] ?? '') ?>">
            <input type="hidden" name="poster_actual" value="<?= htmlspecialchars($temporada['poster'] ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">Serie</label>
                <select name="id_serie" class="form-select" required>
                    <option value="">Selecciona</option>
                    <?php foreach ($series as $serie): ?>
                        <option value="<?= (int)$serie['id'] ?>" <?= $idSeriePre === (int)$serie['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($serie['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de temporada</label>
                <input type="number" name="numero_temporada" class="form-control" required min="1" value="<?= htmlspecialchars($temporada['numero_temporada'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($temporada['titulo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($temporada['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster de temporada</label>
                <input type="file" name="poster_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">

                <?php if (!empty($temporada['poster'])): ?>
                    <div class="mt-3">
                        <p class="mb-2 small text-light">Poster actual:</p>
                        <img src="<?= htmlspecialchars(mm_asset_url($temporada['poster'])) ?>" alt="Poster actual" style="max-width:180px;border-radius:10px;">
                    </div>
                <?php endif; ?>

                <small class="text-secondary d-block mt-2">
                    Si no subes una imagen nueva, se mantiene la actual. Se guarda en assets/img/series/temporadas y se convierte a WebP cuando PHP tiene GD con soporte WebP.
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha estreno</label>
                <input type="date" name="fecha_estreno" class="form-control" value="<?= htmlspecialchars($temporada['fecha_estreno'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear temporada' ?>
                </button>
                <a href="<?= htmlspecialchars(mm_admin_url('series/temporadas/list.php') . ($idSeriePre > 0 ? '?id_serie=' . $idSeriePre : '')) ?>" class="btn btn-outline-light">Volver</a>
                <a href="<?= htmlspecialchars(mm_admin_url('series/panel.php')) ?>" class="btn btn-outline-warning">Resumen</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






