<?php
session_start();
require_once("../config/conexion.php");
require_once(__DIR__ . "/includes/series_admin_ui.php");

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$temporadas = $pdo->query("
    SELECT t.id, t.numero_temporada, s.titulo AS serie_titulo
    FROM temporada t
    INNER JOIN serie s ON t.id_serie = s.id
    ORDER BY s.titulo ASC, t.numero_temporada ASC
")->fetchAll(PDO::FETCH_ASSOC);

$idTemporadaPre = isset($_GET['id_temporada']) ? (int)$_GET['id_temporada'] : 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_temporada = (int)($_POST["id_temporada"] ?? 0);
    $numero_episodio = (int)($_POST["numero_episodio"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $duracion = !empty($_POST["duracion"]) ? (int)$_POST["duracion"] : null;
    $fecha_estreno = !empty($_POST["fecha_estreno"]) ? $_POST["fecha_estreno"] : null;

    if ($id_temporada > 0 && $numero_episodio > 0 && $titulo !== "") {
        $stmt = $pdo->prepare("
            INSERT INTO episodio (id_temporada, numero_episodio, titulo, descripcion, duracion, fecha_estreno)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_temporada, $numero_episodio, $titulo, $descripcion, $duracion, $fecha_estreno]);
        header("Location: episodios.php?id_temporada=" . $id_temporada);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir episodio | MMCINEMA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../navbar.php"); ?>

<div class="container py-4">
    <h1 class="mb-4">Añadir episodio</h1>

    <?php mm_render_series_admin_nav('episodios'); ?>

    <div class="form-card">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Temporada</label>
                <select name="id_temporada" class="form-select" required>
                    <option value="">Selecciona</option>
                    <?php foreach ($temporadas as $temporada): ?>
                        <option value="<?= (int)$temporada['id'] ?>" <?= $idTemporadaPre === (int)$temporada['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($temporada['serie_titulo']) ?> - Temporada <?= (int)$temporada['numero_temporada'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Número de episodio</label>
                <input type="number" name="numero_episodio" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"></textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Duración (minutos)</label>
                    <input type="number" name="duracion" class="form-control" min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha estreno</label>
                    <input type="date" name="fecha_estreno" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary" type="submit">Guardar episodio</button>
                <a href="episodios.php" class="btn btn-outline-light">Volver</a>
                <a href="series_panel.php" class="btn btn-outline-warning">Resumen</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>