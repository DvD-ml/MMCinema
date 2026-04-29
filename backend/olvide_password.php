<?php
require_once "../config/conexion.php";
require_once "../config/mail.php";

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/olvide_password.php?error=email_invalido");
    exit();
}

$stm = $pdo->prepare("
    SELECT id, username, email
    FROM usuario
    WHERE email = ?
    LIMIT 1
");
$stm->execute([$email]);
$usuario = $stm->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $upd = $pdo->prepare("
        UPDATE usuario
        SET reset_token = ?, reset_expira = ?
        WHERE id = ?
    ");
    $upd->execute([$token, $expira, $usuario['id']]);

    enviarCorreoRecuperacion($usuario['email'], $usuario['username'], $token);
}

/*
 No decimos si el email existe o no.
 Así queda más seguro.
*/
header("Location: ../pages/olvide_password.php?ok=1");
exit();