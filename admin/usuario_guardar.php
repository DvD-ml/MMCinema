<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id       = (int)($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$rol      = trim($_POST['rol'] ?? 'usuario');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || !in_array($rol, ['admin', 'usuario'], true)) {
    header("Location: usuarios.php?error=1");
    exit();
}

/* comprobar email repetido */
$sqlEmail = "SELECT id FROM usuario WHERE email = ? AND id != ?";
$stmEmail = $pdo->prepare($sqlEmail);
$stmEmail->execute([$email, $id]);
if ($stmEmail->fetch(PDO::FETCH_ASSOC)) {
    header("Location: usuarios.php?error=email");
    exit();
}

/* comprobar username repetido */
$sqlUser = "SELECT id FROM usuario WHERE username = ? AND id != ?";
$stmUser = $pdo->prepare($sqlUser);
$stmUser->execute([$username, $id]);
if ($stmUser->fetch(PDO::FETCH_ASSOC)) {
    header("Location: usuarios.php?error=username");
    exit();
}

if ($id > 0) {
    /* editar */
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE usuario
                SET username = ?, email = ?, rol = ?, password_hash = ?
                WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$username, $email, $rol, $hash, $id]);
    } else {
        $sql = "UPDATE usuario
                SET username = ?, email = ?, rol = ?
                WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$username, $email, $rol, $id]);
    }
} else {
    /* crear */
    if ($password === '') {
        header("Location: usuarios.php?error=password");
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (username, email, password_hash, rol, verificado)
            VALUES (?, ?, ?, ?, 1)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$username, $email, $hash, $rol]);
}

header("Location: usuarios.php?ok=1");
exit();