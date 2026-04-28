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
  <title>MMCinema | Registro</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container form-container-wrapper">
  <div class="card form-card" style="max-width:520px; width:100%;">
    <h3 class="text-center mb-3">Crear cuenta</h3>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'campos'): ?>
      <div class="alert alert-danger">Debes rellenar todos los campos.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'email_invalido'): ?>
      <div class="alert alert-danger">El correo no tiene un formato válido.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'pass_distintas'): ?>
      <div class="alert alert-danger">Las contraseñas no coinciden.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'pass_corta'): ?>
      <div class="alert alert-danger">La contraseña debe tener al menos 6 caracteres.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'email_duplicado'): ?>
      <div class="alert alert-danger">Ese email ya está registrado.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'bd'): ?>
      <div class="alert alert-danger">Ha ocurrido un error al guardar el usuario.</div>
    <?php endif; ?>

    <form action="backend/registro.php" method="POST" autocomplete="on">
      <div class="mb-3">
        <label class="form-label" for="username">Nombre de usuario</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          class="form-control" 
          autocomplete="username"
          required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control" 
          autocomplete="email"
          required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Contraseña</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          autocomplete="new-password"
          required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password_confirm">Repite la contraseña</label>
        <input 
          type="password" 
          id="password_confirm" 
          name="password_confirm" 
          class="form-control" 
          autocomplete="new-password"
          required>
      </div>

      <button class="btn btn-primary w-100" type="submit">Crear cuenta</button>
    </form>

    <div class="text-center mt-3">
      <small>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></small>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <?php include "includes/lenis-scripts.php"; ?> -->
</body>
</html>