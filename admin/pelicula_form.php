<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pelicula = [
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

if ($id > 0) {
    $stm = $pdo->prepare("SELECT * FROM pelicula WHERE id = ?");
    $stm->execute([$id]);
    $pelicula = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$pelicula) {
        header("Location: peliculas.php");
        exit();
    }
}

$generos = $pdo->query("SELECT id, nombre FROM genero ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id > 0 ? 'Editar película' : 'Nueva película' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $id > 0 ? 'Editar película' : 'Nueva película' ?></h1>
        <a href="peliculas.php" class="btn btn-secondary">Volver</a>
    </div>

    <form action="pelicula_guardar.php" method="POST" enctype="multipart/form-data" class="card card-body bg-black text-white border-secondary">
        <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['id']) ?>">
        <input type="hidden" name="poster_actual" value="<?= htmlspecialchars($pelicula['poster']) ?>">

        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required
                   value="<?= htmlspecialchars($pelicula['titulo']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Sinopsis</label>
            <textarea name="sinopsis" class="form-control" rows="5" required><?= htmlspecialchars($pelicula['sinopsis']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Poster</label>
            <input type="file" name="poster_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <?php if (!empty($pelicula['poster'])): ?>
                <div class="mt-3">
                    <p class="mb-2 small text-light">Poster actual:</p>
                    <img src="../assets/img/posters/<?= htmlspecialchars($pelicula['poster']) ?>" alt="Poster actual" style="max-width: 180px; border-radius: 10px;">
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
                       value="<?= htmlspecialchars($pelicula['fecha_estreno']) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Duración (min)</label>
                <input type="number" name="duracion" class="form-control" required
                       value="<?= htmlspecialchars($pelicula['duracion']) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Edad</label>
                <input type="text" name="edad" class="form-control" required
                       value="<?= htmlspecialchars($pelicula['edad']) ?>"
                       placeholder="+7, +12, +16...">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Género</label>
            <select name="id_genero" class="form-select" required>
                <option value="">Selecciona género</option>
                <?php foreach ($generos as $g): ?>
                    <option value="<?= (int)$g['id'] ?>"
                        <?= ((int)$pelicula['id_genero'] === (int)$g['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Trailer (URL embed)</label>
            <input type="text" name="trailer" class="form-control"
                   value="<?= htmlspecialchars($pelicula['trailer']) ?>">
        </div>

        <button type="submit" class="btn btn-success">
            <?= $id > 0 ? 'Guardar cambios' : 'Crear película' ?>
        </button>
    </form>
</div>
</body>
</html>