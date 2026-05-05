<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pelicula_id_preseleccionada = isset($_GET['pelicula_id']) ? (int)$_GET['pelicula_id'] : 0;
$modoEdicion = $id > 0;

// Generar token CSRF una sola vez
$csrfToken = CSRF::generarToken();

$proyeccion = [
    'id' => '',
    'id_pelicula' => $pelicula_id_preseleccionada,
    'fecha' => '',
    'hora' => '',
    'sala' => ''
];

if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT * FROM proyeccion WHERE id = ?");
    $stm->execute([$id]);
    $proyeccion = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$proyeccion) {
        header("Location: list.php");
        exit();
    }
    $pelicula_id_preseleccionada = $proyeccion['id_pelicula'];
}

// Obtener proyecciones existentes de la película
$proyecciones_existentes = [];
if ($pelicula_id_preseleccionada > 0) {
    $stm = $pdo->prepare("SELECT id, fecha, hora, sala FROM proyeccion WHERE id_pelicula = ? ORDER BY fecha ASC, hora ASC");
    $stm->execute([$pelicula_id_preseleccionada]);
    $proyecciones_existentes = $stm->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="icon" type="image/svg+xml" href="../../../favicon.svg">
    <link rel="stylesheet" href="../../../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .section-header {
            font-size: 16px;
            font-weight: 600;
            color: #f97316;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(249, 115, 22, 0.3);
        }

        .proyecciones-existentes {
            background: rgba(10, 18, 32, 0.6);
            border: 1px solid rgba(249, 115, 22, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 32px;
        }

        .proyecciones-titulo {
            font-size: 16px;
            font-weight: 600;
            color: #f97316;
            margin-bottom: 16px;
            display: none;
        }

        .proyeccion-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(249, 115, 22, 0.1);
            font-size: 13px;
        }

        .proyeccion-row:last-child {
            border-bottom: none;
        }

        .proyeccion-info {
            color: #cbd5e1;
            flex: 1;
        }

        .proyeccion-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm-edit {
            background: #f97316;
            color: #ffffff !important;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none !important;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-sm-edit:hover {
            background: #ea580c;
            color: #ffffff !important;
        }

        .btn-sm-delete {
            background: #f97316;
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-sm-delete:hover {
            background: #ea580c;
            color: #ffffff;
        }

        .empty-proyecciones {
            color: #9ca3af;
            font-size: 13px;
            text-align: center;
            padding: 16px 0;
        }

        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #f97316;
            margin-bottom: 20px;
            margin-top: 32px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(249, 115, 22, 0.3);
        }

        .form-section-title:first-of-type {
            margin-top: 0;
        }

        /* Botón principal del formulario */
        .btn-submit-custom {
            background: #f97316;
            border: none;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-submit-custom:hover {
            background: #ea580c;
            color: #ffffff;
        }
    </style>
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/../../../admin/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar proyección' : 'Nueva proyección' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la proyección.' : 'Crea una nueva proyección de película.' ?></p>
        </div>
        <a href="list.php" class="btn btn-outline-light">Volver</a>
    </div>

    <!-- HEADER SECTION -->
    <!-- PROYECCIONES EXISTENTES -->
    <?php if ($pelicula_id_preseleccionada > 0): ?>
        <div class="proyecciones-existentes">
            <div class="proyecciones-titulo">Proyecciones existentes</div>
            <?php if (empty($proyecciones_existentes)): ?>
                <div class="empty-proyecciones">No hay proyecciones para esta película</div>
            <?php else: ?>
                <?php foreach ($proyecciones_existentes as $proy): ?>
                    <div class="proyeccion-row">
                        <div class="proyeccion-info">
                            <?= date('d/m/Y', strtotime($proy['fecha'])) ?> - <?= substr($proy['hora'], 0, 5) ?> - <?= htmlspecialchars($proy['sala']) ?>
                        </div>
                        <div class="proyeccion-actions">
                            <a href="form.php?id=<?= (int)$proy['id'] ?>" class="btn-sm-edit">Editar</a>
                            <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('¿Eliminar?')">
                                <input type="hidden" name="id" value="<?= (int)$proy['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn-sm-delete">Eliminar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="admin-glass-card p-4">
        <form action="save.php" method="POST">
            <?php echo CSRF::campoFormulario(); ?>
            <input type="hidden" name="id" value="<?= (int)$proyeccion['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Película</label>
                <select name="id_pelicula" class="form-select" required onchange="location.href='form.php?pelicula_id='+this.value">
                    <option value="">Selecciona una película</option>
                    <?php foreach ($peliculas as $p): ?>
                        <option value="<?= (int)$p['id'] ?>" <?= ((int)$pelicula_id_preseleccionada === (int)$p['id']) ? 'selected' : '' ?>>
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

            <div class="d-flex gap-3 flex-wrap">
                <button type="submit" class="btn-submit-custom">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear proyección' ?>
                </button>
                <a href="list.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>






