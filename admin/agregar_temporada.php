<?php
require_once "auth.php";
verificarAuth();

session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");
require_once(__DIR__ . "/auth.php");

$series = $pdo->query("SELECT id, titulo FROM serie ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);
$idSeriePre = isset($_GET['id_serie']) ? (int)$_GET['id_serie'] : 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_serie = (int)($_POST["id_serie"] ?? 0);
    $numero_temporada = (int)($_POST["numero_temporada"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $poster = mm_upload_image($_FILES['poster_file'] ?? [], 'assets/img/series/temporadas', 'temporada_poster');
    $fecha_estreno = !empty($_POST["fecha_estreno"]) ? $_POST["fecha_estreno"] : null;

    if ($id_serie > 0 && $numero_temporada > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO temporada (id_serie, numero_temporada, titulo, descripcion, poster, fecha_estreno)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_serie, $numero_temporada, $titulo, $descripcion, $poster, $fecha_estreno]);
        header("Location: temporadas.php?id_serie=" . $id_serie);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir temporada | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4">
    <h1 class="mb-4">Añadir temporada</h1>

    <?php mm_render_series_admin_nav('temporadas', ['id_serie' => $idSeriePre]); ?>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
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
                <input type="number" name="numero_temporada" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster de temporada</label>
                <input type="file" name="poster_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">
                <small class="text-secondary d-block mt-2">
                    El sistema lo guardará automáticamente en la carpeta de temporadas.
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha estreno</label>
                <input type="date" name="fecha_estreno" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Guardar temporada</button>
                <a href="temporadas.php" class="btn btn-outline-light">Volver</a>
                <a href="series_panel.php" class="btn btn-outline-warning">Resumen</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>