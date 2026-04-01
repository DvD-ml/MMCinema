<?php
require_once "../config/conexion.php";

$token = trim($_POST['token'] ?? '');
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if ($token === '') {
    header("Location: ../login.php?reset=token_invalido");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: ../restablecer_password.php?token=" . urlencode($token) . "&error=pass_distintas");
    exit();
}

if (strlen($pass1) < 6) {
    header("Location: ../restablecer_password.php?token=" . urlencode($token) . "&error=pass_corta");
    exit();
}

$stm = $pdo->prepare("
    SELECT id, reset_expira
    FROM usuario
    WHERE reset_token = ?
    LIMIT 1
");
$stm->execute([$token]);
$usuario = $stm->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../login.php?reset=token_invalido");
    exit();
}

if (empty($usuario['reset_expira']) || strtotime($usuario['reset_expira']) < time()) {
    header("Location: ../login.php?reset=expirado");
    exit();
}

$hash = password_hash($pass1, PASSWORD_DEFAULT);

$upd = $pdo->prepare("
    UPDATE usuario
    SET password_hash = ?,
        reset_token = NULL,
        reset_expira = NULL
    WHERE id = ?
");
$upd->execute([$hash, $usuario['id']]);

header("Location: ../login.php?reset=ok");
exit();