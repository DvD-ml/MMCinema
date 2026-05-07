<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$id       = (int)($_POST['id'] ?? 0);
$action   = $id > 0 ? 'updated' : 'created';
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$rol      = trim($_POST['rol'] ?? 'usuario');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || !in_array($rol, ['admin', 'usuario'], true)) {
    header("Location: list.php?error=1");
    exit();
}

if (mb_strlen($username, 'UTF-8') > 50 || mb_strlen($email, 'UTF-8') > 50) {
    header("Location: list.php?error=length");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: list.php?error=email_invalid");
    exit();
}

// Validar contraseña si es nueva o se está cambiando
if ($id <= 0 && $password === '') {
    // Crear nuevo usuario sin contraseña
    header("Location: list.php?error=password");
    exit();
}

if ($password !== '' && strlen($password) < 8) {
    // Contraseña muy corta
    header("Location: list.php?error=password_weak");
    exit();
}

// Comprobar email repetido
$sqlEmail = "SELECT id FROM usuario WHERE email = ? AND id != ?";
$stmEmail = $pdo->prepare($sqlEmail);
$stmEmail->execute([$email, $id]);
if ($stmEmail->fetch(PDO::FETCH_ASSOC)) {
    header("Location: list.php?error=email");
    exit();
}

// Comprobar username repetido
$sqlUser = "SELECT id FROM usuario WHERE username = ? AND id != ?";
$stmUser = $pdo->prepare($sqlUser);
$stmUser->execute([$username, $id]);
if ($stmUser->fetch(PDO::FETCH_ASSOC)) {
    header("Location: list.php?error=username");
    exit();
}

if ($id > 0) {
    // Editar
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET username = ?, email = ?, rol = ?, password_hash = ? WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$username, $email, $rol, $hash, $id]);
    } else {
        $sql = "UPDATE usuario SET username = ?, email = ?, rol = ? WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$username, $email, $rol, $id]);
    }
} else {
    // Crear
    if ($password === '') {
        header("Location: list.php?error=password");
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuario (username, email, password_hash, rol, verificado) VALUES (?, ?, ?, ?, 1)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$username, $email, $hash, $rol]);
}

header("Location: list.php?ok=" . $action);
exit();
?>




