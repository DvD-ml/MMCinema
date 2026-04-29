<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$pelicula_id = (int)($_POST['pelicula_id'] ?? 0);
$redirect = trim($_POST['redirect'] ?? '../pages/index.php');

if ($pelicula_id <= 0) {
    header("Location: ../pages/index.php");
    exit();
}

if ($redirect === '') {
    $redirect = '../pages/index.php';
}

$stm = $pdo->prepare("SELECT id FROM favorito WHERE id_usuario = ? AND id_pelicula = ? LIMIT 1");
$stm->execute([$usuario_id, $pelicula_id]);
$favorito = $stm->fetch(PDO::FETCH_ASSOC);

if ($favorito) {
    $del = $pdo->prepare("DELETE FROM favorito WHERE id_usuario = ? AND id_pelicula = ?");
    $del->execute([$usuario_id, $pelicula_id]);
} else {
    $ins = $pdo->prepare("INSERT INTO favorito (id_usuario, id_pelicula) VALUES (?, ?)");
    $ins->execute([$usuario_id, $pelicula_id]);
}

header("Location: " . $redirect);
exit();