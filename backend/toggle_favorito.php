<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$pelicula_id = 0;
$redirect = '../pages/index.php';

if (isset($_POST['pelicula_id'])) {
    $pelicula_id = (int)$_POST['pelicula_id'];
}

if (isset($_POST['redirect'])) {
    $redirect = trim($_POST['redirect']);
}

if ($pelicula_id <= 0) {
    header("Location: ../pages/index.php");
    exit();
}

if ($redirect === '') {
    $redirect = '../pages/index.php';
}

$sql = "SELECT id FROM favorito WHERE id_usuario = ? AND id_pelicula = ? LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([$usuario_id, $pelicula_id]);
$favorito = $stm->fetch(PDO::FETCH_ASSOC);

if ($favorito) {
    $sqlDelete = "DELETE FROM favorito WHERE id_usuario = ? AND id_pelicula = ?";
    $del = $pdo->prepare($sqlDelete);
    $del->execute([$usuario_id, $pelicula_id]);
} else {
    $sqlInsert = "INSERT INTO favorito (id_usuario, id_pelicula) VALUES (?, ?)";
    $ins = $pdo->prepare($sqlInsert);
    $ins->execute([$usuario_id, $pelicula_id]);
}

header("Location: " . $redirect);
exit();
?>