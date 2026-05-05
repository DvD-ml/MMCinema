<?php
require_once "../../../auth.php";
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Configurar variables para CRUD genérico
$entity = 'pelicula';
$table = 'pelicula';
$fields = ['titulo', 'sinopsis', 'poster', 'fecha_estreno', 'duracion', 'edad', 'id_genero', 'trailer'];

// Función para obtener opciones de selects
$getSelectOptions = function($pdo) {
    return [
        'id_genero' => $pdo->query("SELECT id, nombre FROM genero ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC)
    ];
};

// Obtener ID del registro
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

// Inicializar datos
$data = [
    'id' => '',
    'titulo' => '',
    'sinopsis' => '',
    'poster' => '',
    'fecha_estreno' => '',
    'duracion' => '',
    'edad' => '',
    'id_genero' => '',
    'trailer' => ''
];

// Si es edición, obtener datos
if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT * FROM pelicula WHERE id = ?");
    $stm->execute([$id]);
    $data = $stm->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        header("Location: list.php");
        exit();
    }
}

// Generar token CSRF
$csrfToken = CSRF::generarToken();

// Obtener opciones para selects
$selectOptions = $getSelectOptions($pdo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar película' : 'Nueva película' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar película' : 'Nueva película' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la película.' : 'Crea una nueva película.' ?></p>
        </div>
        <a href="list.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="save.php" method="POST" enctype="multipart/form-data">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
            <input type="hidden" name="poster_actual" value="<?= htmlspecialchars($data['poster'] ?? '') ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required
                       value="<?= htmlspecialchars($data['titulo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-control" rows="5" required><?= htmlspecialchars($data['sinopsis'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster</label>
                <input type="file" name="poster_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

                <?php if (!empty($data['poster'])): ?>
                    <div class="mt-3">
                        <p class="mb-2 small text-light">Poster actual:</p>
                        <img src="../../../assets/img/posters/<?= htmlspecialchars($data['poster']) ?>" alt="Poster actual" style="max-width: 180px; border-radius: 10px;">
                    </div>
                <?php endif; ?>

                <small class="text-secondary d-block mt-2">
                    Si no seleccionas una imagen nueva, se mantiene la actual.
                </small>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha estreno</label>
                    <input type="date" name="fecha_estreno" class="form-control" required
                           value="<?= htmlspecialchars($data['fecha_estreno'] ?? '') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Duración (min)</label>
                    <input type="number" name="duracion" class="form-control" required
                           value="<?= htmlspecialchars($data['duracion'] ?? '') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Edad</label>
                    <input type="text" name="edad" class="form-control" required
                           value="<?= htmlspecialchars($data['edad'] ?? '') ?>"
                           placeholder="+7, +12, +16...">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Género</label>
                <select name="id_genero" class="form-select" required>
                    <option value="">Selecciona género</option>
                    <?php foreach ($selectOptions['id_genero'] as $g): ?>
                        <option value="<?= (int)$g['id'] ?>"
                            <?= ((int)($data['id_genero'] ?? 0) === (int)$g['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Trailer (URL embed)</label>
                <input type="text" name="trailer" class="form-control"
                       value="<?= htmlspecialchars($data['trailer'] ?? '') ?>">
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn btn-primary btn-lg">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear película' ?>
                </button>
                <a href="list.php" class="btn btn-outline-light btn-lg">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





