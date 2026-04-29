<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");
require_once "../helpers/CSRF.php";

$id = 0;
if (isset($_GET["id"])) {
    $id = (int)$_GET["id"];
}

if ($id <= 0) {
    die("Temporada no válida.");
}

$series = $pdo->query("SELECT id, titulo FROM serie ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM temporada WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$temporada = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$temporada) {
    die("Temporada no encontrada.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    CSRF::validarOAbortar();
    
    $id_serie = 0;
    if (isset($_POST["id_serie"])) {
        $id_serie = (int)$_POST["id_serie"];
    }
    
    $numero_temporada = 0;
    if (isset($_POST["numero_temporada"])) {
        $numero_temporada = (int)$_POST["numero_temporada"];
    }
    
    $titulo = '';
    if (isset($_POST["titulo"])) {
        $titulo = trim($_POST["titulo"]);
    }
    
    $descripcion = '';
    if (isset($_POST["descripcion"])) {
        $descripcion = trim($_POST["descripcion"]);
    }
    
    $posterAnterior = $temporada['poster'] ?? null;
    $poster = $posterAnterior;
    if (isset($_FILES['poster_file'])) {
        $poster = mm_upload_image($_FILES['poster_file'], 'assets/img/series/temporadas', 'temporada_poster', $posterAnterior);
    }
    
    $fecha_estreno = null;
    if (isset($_POST["fecha_estreno"]) && $_POST["fecha_estreno"] !== '') {
        $fecha_estreno = $_POST["fecha_estreno"];
    }

    if ($id_serie > 0 && $numero_temporada > 0) {
        $stmtUpdate = $pdo->prepare("
            UPDATE temporada
            SET id_serie = ?, numero_temporada = ?, titulo = ?, descripcion = ?, poster = ?, fecha_estreno = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([$id_serie, $numero_temporada, $titulo, $descripcion, $poster, $fecha_estreno, $id]);
        header("Location: temporadas.php?id_serie=" . $id_serie);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar temporada | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4">
    <h1 class="mb-4">Editar temporada</h1>

    <?php mm_render_series_admin_nav('temporadas', ['id_serie' => (int)$temporada['id_serie']]); ?>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <?php echo CSRF::campoFormulario(); ?>
            
            <div class="mb-3">
                <label class="form-label">Serie</label>
                <select name="id_serie" class="form-select" required>
                    <option value="">Selecciona</option>
                    <?php foreach ($series as $serie): ?>
                        <option value="<?= (int)$serie['id'] ?>" <?= (int)$temporada['id_serie'] === (int)$serie['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($serie['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de temporada</label>
                <input type="number" name="numero_temporada" class="form-control" value="<?= (int)$temporada['numero_temporada'] ?>" required min="1">
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
                        <img src="../<?= htmlspecialchars($temporada['poster']) ?>" alt="Poster actual" style="max-width:180px;border-radius:10px;">
                    </div>
                <?php endif; ?>

                <small class="text-secondary d-block mt-2">
                    Si no subes una imagen nueva, se mantiene la actual.
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha estreno</label>
                <input type="date" name="fecha_estreno" class="form-control" value="<?= htmlspecialchars($temporada['fecha_estreno'] ?? '') ?>">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a href="temporadas.php?id_serie=<?= (int)$temporada['id_serie'] ?>" class="btn btn-outline-light">Volver</a>
                <a href="series_panel.php" class="btn btn-outline-warning">Resumen</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>