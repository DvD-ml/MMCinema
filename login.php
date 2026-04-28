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
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/custom-checkbox.css">
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
      <div class="alert alert-warning" style="border-left: 4px solid #f59e0b;">
        <h5 class="alert-heading mb-2">⚠️ Cuenta no verificada</h5>
        <p class="mb-2">Debes verificar tu correo electrónico antes de iniciar sesión.</p>
        <p class="mb-2">Revisa tu bandeja de entrada y la carpeta de <strong>spam</strong>.</p>
        <hr style="border-color: rgba(245, 158, 11, 0.3);">
        <p class="mb-0">
          ¿No recibiste el correo? 
          <a href="reenviar_verificacion.php<?php echo isset($_GET['email']) ? '?email=' . urlencode($_GET['email']) : ''; ?>" 
             class="alert-link fw-bold" 
             style="text-decoration: underline;">
            Reenviar correo de verificación →
          </a>
        </p>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reenvio']) && $_GET['reenvio'] === 'ok'): ?>
      <div class="alert alert-success" style="border-left: 4px solid #10b981;">
        <h5 class="alert-heading mb-2">✅ Correo reenviado</h5>
        <p class="mb-0">Te hemos enviado un nuevo correo de verificación. Revisa tu bandeja de entrada y la carpeta de spam.</p>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reenvio']) && $_GET['reenvio'] === 'error'): ?>
      <div class="alert alert-danger" style="border-left: 4px solid #ef4444;">
        <h5 class="alert-heading mb-2">❌ Error al enviar</h5>
        <p class="mb-0">No se pudo reenviar el correo. Inténtalo de nuevo más tarde.</p>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reenvio']) && $_GET['reenvio'] === 'ya_verificado'): ?>
      <div class="alert alert-info" style="border-left: 4px solid #3b82f6;">
        <h5 class="alert-heading mb-2">ℹ️ Cuenta ya verificada</h5>
        <p class="mb-0">Tu cuenta ya está verificada. Puedes iniciar sesión normalmente.</p>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['reenvio']) && $_GET['reenvio'] === 'no_existe'): ?>
      <div class="alert alert-danger" style="border-left: 4px solid #ef4444;">
        <h5 class="alert-heading mb-2">❌ Email no encontrado</h5>
        <p class="mb-0">No existe ninguna cuenta con ese correo electrónico.</p>
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

    <form action="backend/login.php" method="POST" autocomplete="on" id="loginForm">
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control" 
          autocomplete="email"
          required
          autofocus>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Contraseña</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          autocomplete="current-password"
          required>
      </div>

      <div class="mb-3">
        <div class="custom-checkbox-wrapper">
          <input type="checkbox" class="custom-checkbox-input" id="recordar" name="recordar" value="1" checked>
          <label class="custom-checkbox-label" for="recordar">
            <span class="custom-checkbox-box">
              <svg class="custom-checkbox-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </span>
            <span class="custom-checkbox-text">Recordar mi sesión durante 30 días</span>
          </label>
        </div>
      </div>

      <button class="btn btn-primary w-100" type="submit" name="login">Entrar</button>
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
<script>
// Ayudar al navegador a detectar el formulario de login
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    // Asegurar que el navegador reconozca los campos
    emailInput.setAttribute('autocomplete', 'username email');
    passwordInput.setAttribute('autocomplete', 'current-password');
    
    // Marcar el formulario como formulario de login
    form.setAttribute('data-form-type', 'login');
});
</script>
<!-- <?php include "includes/lenis-scripts.php"; ?> -->
</body>
</html>