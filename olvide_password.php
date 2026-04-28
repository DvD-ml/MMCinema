<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | Recuperar contraseúa</title>
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
        <h3 class="text-center mb-3">Recuperar contraseúa</h3>

        <?php if (isset($_GET['ok']) && $_GET['ok'] === '1'): ?>
            <div class="alert alert-success">
                Si el correo existe en nuestra base de datos, te hemos enviado un enlace para restablecer tu contraseúa.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'email_invalido'): ?>
            <div class="alert alert-danger">
                Introduce un email válido.
            </div>
        <?php endif; ?>

        <p class="text-center text-muted mb-4">
            Introduce tu correo y te enviaremos un enlace para cambiar tu contraseúa.
        </p>

        <form action="backend/olvide_password.php" method="POST">
            <div class="mb-3">
                <label class="form-label" for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100" type="submit">Enviar enlace</button>
        </form>

        <div class="text-center mt-3">
            <small><a href="login.php">Volver al login</a></small>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <?php include "includes/lenis-scripts.php"; ?> -->
</body>
</html>