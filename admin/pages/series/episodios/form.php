<?php
require_once "../../../../auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/series_admin_ui.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

// Inicializar datos
$episodio = [
    'id' => '',
    'id_temporada' => '',
    'numero_episodio' => '',
    'titulo' => '',
    'descripcion' => '',
    'duracion' => '',
    'fecha_estreno' => ''
];

// Si es edición, obtener datos
if ($modoEdicion) {
    $stmt = $pdo->prepare("SELECT * FROM episodio WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $episodio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$episodio) {
        header("Location: list.php");
        exit();
    }
}

// Obtener temporada preseleccionada (si viene en GET)
$idTemporadaPre = isset($_GET['id_temporada']) ? (int)$_GET['id_temporada'] : (int)($episodio['id_temporada'] ?? 0);

// Obtener opciones
$temporadas = $pdo->query("
    SELECT t.id, t.numero_temporada, s.titulo AS serie_titulo
    FROM temporada t
    INNER JOIN serie s ON t.id_serie = s.id
    ORDER BY s.titulo ASC, t.numero_temporada ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Generar token CSRF
$csrfToken = CSRF::generarToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar episodio' : 'Añadir episodio' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../../../../favicon.svg">
    <link rel="stylesheet" href="../../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../../admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar episodio' : 'Añadir episodio' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos del episodio.' : 'Crea un nuevo episodio.' ?></p>
        </div>
        <a href="list.php<?= $idTemporadaPre > 0 ? '?id_temporada=' . $idTemporadaPre : '' ?>" class="btn btn-outline-light">Volver</a>
    </div>

    <?php mm_render_series_admin_nav('episodios'); ?>

    <div class="admin-glass-card p-4">
        <form method="POST" action="save.php">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($episodio['id'] ?? '') ?>">

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
                <input type="number" name="numero_episodio" class="form-control" required min="1" value="<?= htmlspecialchars($episodio['numero_episodio'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($episodio['titulo'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"><?= htmlspecialchars($episodio['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Duración (minutos)</label>
                    <input type="number" name="duracion" class="form-control" min="1" value="<?= htmlspecialchars($episodio['duracion'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha estreno</label>
                    <input type="date" name="fecha_estreno" class="form-control" value="<?= htmlspecialchars($episodio['fecha_estreno'] ?? '') ?>">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-3">
                <button class="btn btn-primary btn-lg" type="submit">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear episodio' ?>
                </button>
                <a href="list.php<?= $idTemporadaPre > 0 ? '?id_temporada=' . $idTemporadaPre : '' ?>" class="btn btn-outline-light btn-lg">Volver</a>
                <a href="../panel.php" class="btn btn-outline-warning btn-lg">Resumen</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






