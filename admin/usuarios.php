<?php
require_once "auth.php";
require_once "../config/conexion.php";

verificarAuth();

$sql = "SELECT id, username, email, rol, creado FROM usuario ORDER BY id DESC";
$usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<?php require_once __DIR__ . "/admin_header.php"; ?>

<div class="container py-4 py-lg-5">
    <div class="admin-page-head">
        <div>
            <h1>Usuarios</h1>
            <p>Revisa roles, correos, crea nuevas cuentas y elimina las que ya no necesites.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="usuario_form.php" class="btn btn-primary">Aúadir usuario</a>
            <a href="index.php" class="btn btn-outline-light">Volver al panel</a>
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success">Usuario guardado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            switch ($_GET['error']) {
                case 'email':
                    echo "Ya existe otro usuario con ese email.";
                    break;
                case 'username':
                    echo "Ya existe otro usuario con ese nombre de usuario.";
                    break;
                case 'password':
                    echo "Debes indicar una contraseúa al crear un usuario.";
                    break;
                default:
                    echo "No se pudo guardar el usuario.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="admin-glass-card p-3 p-lg-4">
        <div class="admin-table-wrap">
            <table class="admin-table table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= (int)$u['id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?= $u['rol'] === 'admin'
                                    ? '<span class="badge bg-danger">admin</span>'
                                    : '<span class="badge bg-secondary">usuario</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($u['creado'] ?? '-') ?></td>
                            <td>
                                <div class="admin-actions d-flex gap-2 flex-wrap">
                                    <a href="usuario_form.php?id=<?= (int)$u['id'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                    <?php if ((int)$u['id'] !== (int)$_SESSION['usuario_id']): ?>
                                        <a href="usuario_borrar.php?id=<?= (int)$u['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Seguro que quieres borrar este usuario?')">
                                            Borrar
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Tu cuenta</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>