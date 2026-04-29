<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pelicula_id_preseleccionada = isset($_GET['pelicula_id']) ? (int)$_GET['pelicula_id'] : 0;
$modoEdicion = $id > 0;

$proyeccion = [
    'id' => '',
    'id_pelicula' => $pelicula_id_preseleccionada, // Pre-seleccionar si viene de la URL
    'fecha' => '',
    'hora' => '',
    'sala' => ''
];

if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT * FROM proyeccion WHERE id = ?");
    $stm->execute([$id]);
    $proyeccion = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$proyeccion) {
        header("Location: proyecciones.php");
        exit();
    }
}

$peliculas = $pdo->query("SELECT id, titulo FROM pelicula ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);
$salas = $pdo->query("SELECT sala FROM sala_config ORDER BY sala ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar proyección' : 'Nueva proyección' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar proyección' : 'Nueva proyección' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la proyección.' : 'Crea una nueva proyección de película.' ?></p>
        </div>
        <a href="proyecciones.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="proyeccion_guardar.php" method="POST">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= (int)$proyeccion['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Película</label>
                <select name="id_pelicula" class="form-select" required>
                    <option value="">Selecciona una película</option>
                    <?php foreach ($peliculas as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= ((int)$proyeccion['id_pelicula'] === (int)$p['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha</label>
                    <input
                        type="date"
                        name="fecha"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($proyeccion['fecha']) ?>"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Hora</label>
                    <input
                        type="time"
                        name="hora"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($proyeccion['hora']) ?>"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Sala</label>
                <select name="sala" class="form-select" required>
                    <option value="">Selecciona una sala</option>
                    <?php foreach ($salas as $s): ?>
                        <option value="<?= htmlspecialchars($s['sala']) ?>" <?= ($proyeccion['sala'] === $s['sala']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['sala']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear proyección' ?>
                </button>
                <a href="proyecciones.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
