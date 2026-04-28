<?php
session_start();
require_once "../config/conexion.php";
require_once "../config/mail.php";
require_once "../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$pass1 = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if ($username === '' || $email === '' || $pass1 === '' || $pass2 === '') {
    header("Location: ../registro.php?error=campos");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../registro.php?error=email_invalido");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: ../registro.php?error=pass_distintas");
    exit();
}

if (strlen($pass1) < 6) {
    header("Location: ../registro.php?error=pass_corta");
    exit();
}

$stm = $pdo->prepare("SELECT id FROM usuario WHERE email = ? LIMIT 1");
$stm->execute([$email]);

if ($stm->fetch()) {
    header("Location: ../registro.php?error=email_duplicado");
    exit();
}

$hash = password_hash($pass1, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));
$expira = date('Y-m-d H:i:s', strtotime('+1 day'));

$stm = $pdo->prepare("
    INSERT INTO usuario (username, email, password_hash, creado, verificado, token_verificacion, token_expira)
    VALUES (?, ?, ?, NOW(), 0, ?, ?)
");

try {
    $stm->execute([$username, $email, $hash, $token, $expira]);

    $correoEnviado = enviarCorreoVerificacion($email, $username, $token);

    if ($correoEnviado) {
        header("Location: ../login.php?registro=1&verificacion=1");
        exit();
    } else {
        header("Location: ../login.php?registro=1&verificacion=0");
        exit();
    }
} catch (PDOException $e) {
    header("Location: ../registro.php?error=bd");
    exit();
}