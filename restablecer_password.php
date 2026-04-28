<?php
require_once "config/conexion.php";

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    header("Location: login.php?reset=token_invalido");
    exit();
}

$stm = $pdo->prepare("
    SELECT id, username, reset_expira
    FROM usuario
    WHERE reset_token = ?
    LIMIT 1
");
$stm->execute([$token]);
$usuario = $stm->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: login.php?reset=token_invalido");
    exit();
}

if (empty($usuario['reset_expira']) || strtotime($usuario['reset_expira']) < time()) {
    header("Location: login.php?reset=expirado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Nueva contraseúa</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container form-container-wrapper">
    <div class="card form-card" style="max-width:520px; width:100%;">
        <h3 class="text-center mb-3">Crear nueva contraseúa</h3>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'pass_corta'): ?>
            <div class="alert alert-danger">La contraseúa debe tener al menos 6 caracteres.</div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'pass_distintas'): ?>
            <div class="alert alert-danger">Las contraseúas no coinciden.</div>
        <?php endif; ?>

        <form action="backend/restablecer_password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label" for="password">Nueva contraseúa</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password_confirm">Repite la nueva contraseúa</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100" type="submit">Guardar nueva contraseúa</button>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <?php include "includes/lenis-scripts.php"; ?> -->
</body>
</html>