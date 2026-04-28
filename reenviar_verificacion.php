<?php
session_start();
require_once "config/conexion.php";
require_once "config/mail.php";
require_once "helpers/Logger.php";

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');

// Si no hay email, mostrar formulario
if (empty($email)) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title>MMCinema | Reenviar VerificaciÃ³n</title>
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
            <h3 class="text-center mb-3">Reenviar VerificaciÃ³n</h3>
            
            <div class="alert alert-info" style="border-left: 4px solid #3b82f6;">
                <h5 class="alert-heading mb-2">ðŸ“§ Reenviar correo de verificaciÃ³n</h5>
                <p class="mb-0">Introduce tu email y te enviaremos un nuevo enlace de verificaciÃ³n.</p>
            </div>

            <form action="reenviar_verificacion.php" method="POST">
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        required
                        autofocus>
                </div>

                <button class="btn btn-primary w-100" type="submit">Reenviar correo</button>
            </form>

            <div class="text-center mt-3">
                <small><a href="login.php">â† Volver al login</a></small>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <?php include "includes/lenis-scripts.php"; ?> -->
    </body>
    </html>
    <?php
    exit();
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Logger::warning("Intento de reenvÃ­o con email invÃ¡lido", ['email' => $email]);
    header("Location: login.php?reenvio=error");
    exit();
}

// Buscar usuario
$stmt = $pdo->prepare("
    SELECT id, username, email, verificado, token_verificacion, token_expira
    FROM usuario
    WHERE email = ?
    LIMIT 1
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el usuario
if (!$user) {
    Logger::warning("Intento de reenvÃ­o con email inexistente", ['email' => $email]);
    header("Location: login.php?reenvio=no_existe");
    exit();
}

// Si ya estÃ¡ verificado
if ((int)$user['verificado'] === 1) {
    Logger::info("Intento de reenvÃ­o a cuenta ya verificada", ['user_id' => $user['id']]);
    header("Location: login.php?reenvio=ya_verificado");
    exit();
}

// Generar nuevo token (el anterior puede haber expirado)
$nuevoToken = bin2hex(random_bytes(32));
$nuevaExpiracion = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Actualizar token en BD
$stmtUpdate = $pdo->prepare("
    UPDATE usuario 
    SET token_verificacion = ?, token_expira = ?
    WHERE id = ?
");
$stmtUpdate->execute([$nuevoToken, $nuevaExpiracion, $user['id']]);

// Enviar correo
try {
    $correoEnviado = enviarCorreoVerificacion($email, $user['username'], $nuevoToken);
    
    if ($correoEnviado) {
        Logger::info("Correo de verificaciÃ³n reenviado", [
            'user_id' => $user['id'],
            'email' => $email
        ]);
        header("Location: login.php?reenvio=ok");
        exit();
    } else {
        Logger::error("Error al reenviar correo de verificaciÃ³n", [
            'user_id' => $user['id'],
            'email' => $email
        ]);
        header("Location: login.php?reenvio=error");
        exit();
    }
} catch (Exception $e) {
    Logger::error("ExcepciÃ³n al reenviar correo de verificaciÃ³n", [
        'user_id' => $user['id'],
        'email' => $email,
        'error' => $e->getMessage()
    ]);
    header("Location: login.php?reenvio=error");
    exit();
}



