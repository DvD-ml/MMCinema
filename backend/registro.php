<?php
session_start();
require_once "../config/conexion.php";
require_once "../config/mail.php";
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

$username = '';
$email = '';
$pass1 = '';
$pass2 = '';

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
}

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}

if (isset($_POST['password'])) {
    $pass1 = $_POST['password'];
}

if (isset($_POST['password_confirm'])) {
    $pass2 = $_POST['password_confirm'];
}

if ($username === '' || $email === '' || $pass1 === '' || $pass2 === '') {
    header("Location: ../pages/registro.php?error=campos");
    exit();
}

$emailValido = filter_var($email, FILTER_VALIDATE_EMAIL);
if (!$emailValido) {
    header("Location: ../pages/registro.php?error=email_invalido");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: ../pages/registro.php?error=pass_distintas");
    exit();
}

$longitudPass = strlen($pass1);
if ($longitudPass < 6) {
    header("Location: ../pages/registro.php?error=pass_corta");
    exit();
}

$sql = "SELECT id FROM usuario WHERE email = ? LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([$email]);
$usuarioExistente = $stm->fetch();

if ($usuarioExistente) {
    header("Location: ../pages/registro.php?error=email_duplicado");
    exit();
}

$hash = password_hash($pass1, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));
$fechaActual = date('Y-m-d H:i:s');
$fechaExpira = date('Y-m-d H:i:s', strtotime('+1 day'));

$sql = "INSERT INTO usuario (username, email, password_hash, creado, verificado, token_verificacion, token_expira) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stm = $pdo->prepare($sql);

try {
    $stm->execute([$username, $email, $hash, $fechaActual, 0, $token, $fechaExpira]);

    // Agregar a cola de emails (no bloquea)
    $sql = "INSERT INTO email_queue (tipo, destinatario_email, destinatario_nombre, token) VALUES (?, ?, ?, ?)";
    $stm = $pdo->prepare($sql);
    $stm->execute(['verificacion', $email, $username, $token]);

    header("Location: ../pages/login.php?registro=1&verificacion=1");
    exit();
} catch (PDOException $e) {
    header("Location: ../pages/registro.php?error=bd");
    exit();
}
?>