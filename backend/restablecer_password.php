<?php
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

$token = trim($_POST['token'] ?? '');
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if ($token === '') {
    header("Location: ../pages/login.php?reset=token_invalido");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: ../pages/restablecer_password.php?token=" . urlencode($token) . "&error=pass_distintas");
    exit();
}

if (strlen($pass1) < 6) {
    header("Location: ../pages/restablecer_password.php?token=" . urlencode($token) . "&error=pass_corta");
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
    header("Location: ../pages/login.php?reset=token_invalido");
    exit();
}

if (empty($usuario['reset_expira']) || strtotime($usuario['reset_expira']) < time()) {
    header("Location: ../pages/login.php?reset=expirado");
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

header("Location: ../pages/login.php?reset=ok");
exit();