<?php
require_once "auth.php";
require_once "../config/conexion.php";

$sala = [
    'sala' => '',
    'filas' => 8,
    'columnas' => 10
];

$modoEdicion = false;

if (isset($_GET['sala'])) {
    $nombreSala = $_GET['sala'];
    $stm = $pdo->prepare("SELECT * FROM sala_config WHERE sala = ?");
    $stm->execute([$nombreSala]);
    $datos = $stm->fetch(PDO::FETCH_ASSOC);
    
    if ($datos) {
        $sala = $datos;
        $modoEdicion = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar sala' : 'Nueva sala' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar sala' : 'Nueva sala' ?></h1>
            <p><?= $modoEdicion ? 'Modifica la configuración de la sala.' : 'Crea una nueva sala de cine.' ?></p>
        </div>
        <a href="salas.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="sala_guardar.php" method="POST">
            <?php require_once "../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="sala_anterior" value="<?= htmlspecialchars($sala['sala']) ?>">

            <div class="mb-3">
                <label class="form-label">Nombre de la sala</label>
                <input
                    type="text"
                    name="sala"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($sala['sala']) ?>"
                    placeholder="Ej: Sala 1, Sala VIP, etc."
                    <?= $modoEdicion ? 'readonly' : '' ?>
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Filas</label>
                    <input
                        type="number"
                        name="filas"
                        class="form-control"
                        required
                        min="1"
                        max="20"
                        value="<?= (int)$sala['filas'] ?>"
                    >
                    <small class="text-secondary">Número de filas de asientos</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Columnas</label>
                    <input
                        type="number"
                        name="columnas"
                        class="form-control"
                        required
                        min="1"
                        max="20"
                        value="<?= (int)$sala['columnas'] ?>"
                    >
                    <small class="text-secondary">Número de columnas de asientos</small>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Capacidad total:</strong> <span id="capacidad"><?= (int)$sala['filas'] * (int)$sala['columnas'] ?></span> asientos
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear sala' ?>
                </button>
                <a href="salas.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('input[name="filas"]').addEventListener('change', actualizarCapacidad);
document.querySelector('input[name="columnas"]').addEventListener('change', actualizarCapacidad);

function actualizarCapacidad() {
    const filas = parseInt(document.querySelector('input[name="filas"]').value) || 0;
    const columnas = parseInt(document.querySelector('input[name="columnas"]').value) || 0;
    document.getElementById('capacidad').textContent = filas * columnas;
}
</script>

</body>
</html>
