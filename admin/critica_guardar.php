<?php
require_once "auth.php";
require_once "../config/conexion.php";

$tipo = $_GET['tipo'] ?? 'pelicula';
if (!in_array($tipo, ['pelicula', 'serie'], true)) {
    $tipo = 'pelicula';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

$usuarios = $pdo->query("SELECT id, username, email FROM usuario ORDER BY username ASC")->fetchAll(PDO::FETCH_ASSOC);
$peliculas = $pdo->query("SELECT id, titulo FROM pelicula ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);
$series = $pdo->query("SELECT id, titulo FROM serie ORDER BY titulo ASC")->fetchAll(PDO::FETCH_ASSOC);

$critica = [
    'id' => 0,
    'id_usuario' => '',
    'id_pelicula' => '',
    'id_serie' => '',
    'contenido' => '',
    'puntuacion' => 5
];

if ($modoEdicion) {
    if ($tipo === 'pelicula') {
        $stm = $pdo->prepare("SELECT * FROM critica WHERE id = ?");
    } else {
        $stm = $pdo->prepare("SELECT * FROM critica_serie WHERE id = ?");
    }

    $stm->execute([$id]);
    $datos = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        header("Location: criticas.php");
        exit();
    }

    $critica = $datos;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar crítica' : 'Añadir crítica' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar crítica' : 'Añadir crítica' ?></h1>
            <p><?= $tipo === 'pelicula' ? 'Gestiona una crítica de película.' : 'Gestiona una crítica de serie.' ?></p>
        </div>
        <a href="criticas.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="critica_guardar.php" method="POST">
            <input type="hidden" name="id" value="<?= (int)$critica['id'] ?>">
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <select name="id_usuario" class="form-select" required>
                    <option value="">Selecciona un usuario</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option
                            value="<?= (int)$u['id'] ?>"
                            <?= (int)$critica['id_usuario'] === (int)$u['id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($tipo === 'pelicula'): ?>
                <div class="mb-3">
                    <label class="form-label">Película</label>
                    <select name="id_objeto" class="form-select" required>
                        <option value="">Selecciona una película</option>
                        <?php foreach ($peliculas as $p): ?>
                            <option
                                value="<?= (int)$p['id'] ?>"
                                <?= (int)$critica['id_pelicula'] === (int)$p['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($p['titulo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Serie</label>
                    <select name="id_objeto" class="form-select" required>
                        <option value="">Selecciona una serie</option>
                        <?php foreach ($series as $s): ?>
                            <option
                                value="<?= (int)$s['id'] ?>"
                                <?= (int)$critica['id_serie'] === (int)$s['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($s['titulo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Puntuación</label>
                <select name="puntuacion" class="form-select" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= (int)$critica['puntuacion'] === $i ? 'selected' : '' ?>>
                            <?= $i ?>/5
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Comentario</label>
                <textarea name="contenido" class="form-control" rows="6" required><?= htmlspecialchars($critica['contenido']) ?></textarea>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear crítica' ?>
                </button>
                <a href="criticas.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>