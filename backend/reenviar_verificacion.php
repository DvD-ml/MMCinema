<?php
session_start();
require_once "../config/conexion.php";
require_once "../config/mail.php";
require_once "../helpers/Logger.php";

$email = trim($_POST['email'] ?? '');

// Validar que el email no esté vacío
if ($email === '') {
    header("Location: ../pages/reenviar_verificacion.php?error=email_vacio");
    exit();
}

// Validar formato del email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/reenviar_verificacion.php?error=email_invalido&email=" . urlencode($email));
    exit();
}

// Buscar el usuario en la base de datos
$stm = $pdo->prepare("
    SELECT id, username, email, verificado, token_verificacion, token_expira
    FROM usuario
    WHERE email = ?
    LIMIT 1
");
$stm->execute([$email]);
$user = $stm->fetch(PDO::FETCH_ASSOC);

// Si el usuario no existe
if (!$user) {
    Logger::warning("Intento de reenvío de verificación con email inexistente", ['email' => $email]);
    header("Location: ../pages/login.php?reenvio=no_existe");
    exit();
}

// Si la cuenta ya está verificada
if ((int)$user['verificado'] === 1) {
    Logger::info("Intento de reenvío de verificación en cuenta ya verificada", [
        'user_id' => $user['id'],
        'email' => $email
    ]);
    header("Location: ../pages/login.php?reenvio=ya_verificado");
    exit();
}

// Generar nuevo token de verificación
$nuevoToken = bin2hex(random_bytes(32));
$nuevaExpiracion = date('Y-m-d H:i:s', strtotime('+1 day'));

// Actualizar el token en la base de datos
$stmUpdate = $pdo->prepare("
    UPDATE usuario 
    SET token_verificacion = ?, token_expira = ?
    WHERE id = ?
");
$stmUpdate->execute([$nuevoToken, $nuevaExpiracion, $user['id']]);

// Enviar el correo de verificación
try {
    $correoEnviado = enviarCorreoVerificacion($email, $user['username'], $nuevoToken);
    
    if ($correoEnviado) {
        Logger::info("Correo de verificación reenviado exitosamente", [
            'user_id' => $user['id'],
            'email' => $email
        ]);
        header("Location: ../pages/login.php?reenvio=ok");
        exit();
    } else {
        Logger::error("Error al reenviar correo de verificación", null, [
            'user_id' => $user['id'],
            'email' => $email
        ]);
        header("Location: ../pages/login.php?reenvio=error");
        exit();
    }
} catch (Exception $e) {
    Logger::error("Excepción al reenviar correo de verificación", $e, [
        'user_id' => $user['id'],
        'email' => $email,
        'error' => $e->getMessage()
    ]);
    header("Location: ../pages/login.php?reenvio=error");
    exit();
}
