<?php
require_once "../config/conexion.php";
require_once "../config/mail.php";

$email = '';
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}

$esEmailValido = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$esEmailValido) {
    header("Location: ../pages/olvide_password.php?error=email_invalido");
    exit();
}

$sql = "SELECT id, username, email FROM usuario WHERE email = ? LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([$email]);
$usuario = $stm->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $token = bin2hex(random_bytes(32));
    $fechaActual = date('Y-m-d H:i:s');
    $fechaExpira = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $sqlUpdate = "UPDATE usuario SET reset_token = ?, reset_expira = ? WHERE id = ?";
    $upd = $pdo->prepare($sqlUpdate);
    $upd->execute([$token, $fechaExpira, $usuario['id']]);

    enviarCorreoRecuperacion($usuario['email'], $usuario['username'], $token);
}

header("Location: ../pages/olvide_password.php?ok=1");
exit();
?>