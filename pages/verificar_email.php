<?php
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/mail.php";

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    header("Location: login.php?verificacion=token_invalido");
    exit();
}

$stm = $pdo->prepare("
    SELECT id, username, email, verificado, token_expira
    FROM usuario
    WHERE token_verificacion = ?
    LIMIT 1
");
$stm->execute([$token]);
$usuario = $stm->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: login.php?verificacion=token_invalido");
    exit();
}

if ((int)$usuario['verificado'] === 1) {
    header("Location: login.php?verificacion=ya_verificado");
    exit();
}

if (empty($usuario['token_expira']) || strtotime($usuario['token_expira']) < time()) {
    header("Location: login.php?verificacion=expirada");
    exit();
}

$upd = $pdo->prepare("
    UPDATE usuario
    SET verificado = 1,
        token_verificacion = NULL,
        token_expira = NULL
    WHERE id = ?
");
$upd->execute([(int)$usuario['id']]);

enviarCorreoBienvenida($usuario['email'], $usuario['username']);

header("Location: login.php?verificacion=ok");
exit();
