<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $id > 0;

$usuario = [
    'id' => 0,
    'username' => '',
    'email' => '',
    'rol' => 'usuario'
];

if ($modoEdicion) {
    $stm = $pdo->prepare("SELECT id, username, email, rol FROM usuario WHERE id = ?");
    $stm->execute([$id]);
    $usuario = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: usuarios.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modoEdicion ? 'Editar usuario' : 'Añadir usuario' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1><?= $modoEdicion ? 'Editar usuario' : 'Añadir usuario' ?></h1>
            <p><?= $modoEdicion ? 'Modifica los datos de la cuenta.' : 'Crea una nueva cuenta desde el panel de administración.' ?></p>
        </div>
        <a href="usuarios.php" class="btn btn-outline-light">Volver</a>
    </div>

    <div class="admin-glass-card p-4">
        <form action="usuario_guardar.php" method="POST">
            <input type="hidden" name="id" value="<?= (int)$usuario['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nombre de usuario</label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($usuario['username']) ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($usuario['email']) ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select" required>
                    <option value="usuario" <?= $usuario['rol'] === 'usuario' ? 'selected' : '' ?>>usuario</option>
                    <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>admin</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <?= $modoEdicion ? 'Nueva contraseña' : 'Contraseña' ?>
                </label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    <?= $modoEdicion ? '' : 'required' ?>
                    placeholder="<?= $modoEdicion ? 'Déjala vacía para no cambiarla' : 'Escribe la contraseña del usuario' ?>"
                >
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear usuario' ?>
                </button>
                <a href="usuarios.php" class="btn btn-outline-light">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>