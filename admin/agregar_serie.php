<?php
require_once "auth.php";
verificarAuth();

session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");
require_once(__DIR__ . "/includes/upload_helper.php");
require_once(__DIR__ . "/auth.php");

$generos = $pdo->query("SELECT id, nombre FROM genero ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$plataformas = $pdo->query("SELECT id, nombre FROM plataforma ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $sinopsis = trim($_POST["sinopsis"] ?? "");
    $poster = mm_upload_image($_FILES['poster_file'] ?? [], 'assets/img/series/posters', 'serie_poster');
    $banner = mm_upload_image($_FILES['banner_file'] ?? [], 'assets/img/series/banners', 'serie_banner');
    $fecha_estreno = !empty($_POST["fecha_estreno"]) ? $_POST["fecha_estreno"] : null;
    $edad = trim($_POST["edad"] ?? "");
    $id_genero = !empty($_POST["id_genero"]) ? (int)$_POST["id_genero"] : null;
    $id_plataforma = !empty($_POST["id_plataforma"]) ? (int)$_POST["id_plataforma"] : null;
    $estado = $_POST["estado"] ?? "en_emision";
    $destacada = isset($_POST["destacada"]) ? 1 : 0;
    $trailer = trim($_POST["trailer"] ?? "");

    if ($titulo !== "" && $sinopsis !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO serie
            (titulo, sinopsis, poster, banner, fecha_estreno, edad, id_genero, id_plataforma, estado, destacada, trailer)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $titulo,
            $sinopsis,
            $poster,
            $banner,
            $fecha_estreno,
            $edad,
            $id_genero,
            $id_plataforma,
            $estado,
            $destacada,
            $trailer !== '' ? $trailer : null
        ]);

        header("Location: series.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir serie | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">

<?php require_once __DIR__ . "/admin_header.php"; ?>
require_once "../helpers/FileValidation.php";

<div class="container py-4">
    <h1 class="mb-4">Añadir nueva serie</h1>

    <?php mm_render_series_admin_nav('series'); ?>

    <div class="form-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Sinopsis</label>
                <textarea name="sinopsis" class="form-control" rows="5" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Poster</label>
                <input type="file" name="poster_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">
                <small class="text-secondary d-block mt-2">
                    La imagen se guarda automáticamente en la carpeta de pósters de series.
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label">Banner</label>
                <input type="file" name="banner_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif">
                <small class="text-secondary d-block mt-2">
                    La imagen se guarda automáticamente en la carpeta de banners de series.
                </small>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha estreno</label>
                    <input type="date" name="fecha_estreno" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Edad</label>
                    <input type="text" name="edad" class="form-control" placeholder="+16, +18, TP...">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="en_emision">En emisión</option>
                        <option value="finalizada">Finalizada</option>
                        <option value="proximamente">Próximamente</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">Género</label>
                    <select name="id_genero" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($generos as $genero): ?>
                            <option value="<?= (int)$genero['id'] ?>"><?= htmlspecialchars($genero['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Plataforma</label>
                    <select name="id_plataforma" class="form-select">
                        <option value="">Selecciona</option>
                        <?php foreach ($plataformas as $plataforma): ?>
                            <option value="<?= (int)$plataforma['id'] ?>"><?= htmlspecialchars($plataforma['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label">Trailer</label>
                <input type="text" name="trailer" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                <small class="text-secondary d-block mt-2">
                    Puedes pegar la URL del trailer.
                </small>
            </div>

            <div class="form-check mt-3 mb-3">
                <input class="form-check-input" type="checkbox" name="destacada" id="destacada">
                <label class="form-check-label" for="destacada">Marcar como destacada</label>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Guardar serie</button>
                <a href="series.php" class="btn btn-outline-light">Volver</a>
                <a href="series_panel.php" class="btn btn-outline-warning">Resumen</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>