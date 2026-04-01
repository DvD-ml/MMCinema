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
  <title>MMCinema | Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container form-container-wrapper">
  <div class="card form-card" style="max-width:520px; width:100%;">
    <h3 class="text-center mb-3">Iniciar sesión</h3>

    <?php if (isset($_GET['registro']) && $_GET['registro'] == '1' && isset($_GET['verificacion']) && $_GET['verificacion'] == '1'): ?>
      <div class="alert alert-success">
        Registro completado. Te hemos enviado un correo para verificar tu cuenta.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['registro']) && $_GET['registro'] == '1' && isset($_GET['verificacion']) && $_GET['verificacion'] == '0'): ?>
      <div class="alert alert-warning">
        Registro completado, pero no se pudo enviar el correo de verificación.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['verificacion']) && $_GET['verificacion'] === 'ok'): ?>
      <div class="alert alert-success">
        Tu cuenta ha sido verificada correctamente. Ya puedes iniciar sesión.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['verificacion']) && $_GET['verificacion'] === 'ya_verificado'): ?>
      <div class="alert alert-info">
        Esa cuenta ya estaba verificada.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['verificacion']) && $_GET['verificacion'] === 'token_invalido'): ?>
      <div class="alert alert-danger">
        El enlace de verificación no es válido.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['verificacion']) && $_GET['verificacion'] === 'expirada'): ?>
      <div class="alert alert-danger">
        El enlace de verificación ha caducado.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === '1'): ?>
      <div class="alert alert-danger">Email o contraseña incorrectos.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_verificado'): ?>
      <div class="alert alert-warning">
        Debes verificar tu correo antes de iniciar sesión.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reset']) && $_GET['reset'] === 'ok'): ?>
      <div class="alert alert-success">
        Tu contraseña se ha cambiado correctamente. Ya puedes iniciar sesión.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reset']) && $_GET['reset'] === 'token_invalido'): ?>
      <div class="alert alert-danger">
        El enlace para recuperar la contraseña no es válido.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reset']) && $_GET['reset'] === 'expirado'): ?>
      <div class="alert alert-danger">
        El enlace para recuperar la contraseña ha caducado.
      </div>
    <?php endif; ?>

    <form action="backend/login.php" method="POST">
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>

      <button class="btn btn-primary w-100" type="submit">Entrar</button>
    </form>
    <div class="text-center mt-3">
      <small><a href="olvide_password.php">¿Has olvidado tu contraseña?</a></small>
    </div>

    <div class="text-center mt-3">
      <small>¿No tienes cuenta? <a href="registro.php">Regístrate</a></small>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>