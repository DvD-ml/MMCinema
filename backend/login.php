<?php
session_start();
require_once "../config/conexion.php";

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
    header("Location: ../login.php?error=1");
    exit();
}

$stm = $pdo->prepare("
    SELECT id, username, email, password_hash, rol, verificado
    FROM usuario
    WHERE email = ?
    LIMIT 1
");
$stm->execute([$email]);
$user = $stm->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../login.php?error=1");
    exit();
}

if (!password_verify($pass, $user['password_hash'])) {
    header("Location: ../login.php?error=1");
    exit();
}

if ((int)$user['verificado'] !== 1) {
    header("Location: ../login.php?error=no_verificado");
    exit();
}

$_SESSION['usuario_id'] = (int)$user['id'];
$_SESSION['usuario']    = $user['username'];
$_SESSION['email']      = $user['email'];
$_SESSION['rol']        = $user['rol'];

header("Location: ../index.php");
exit();