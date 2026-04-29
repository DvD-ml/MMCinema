<?php
session_start();
require_once "../config/conexion.php";
require_once "../helpers/Logger.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$serie_id = 0;
$redirect = 'series.php';

if (isset($_POST['serie_id'])) {
    $serie_id = (int)$_POST['serie_id'];
}

if (isset($_POST['redirect'])) {
    $redirect = $_POST['redirect'];
}

$redirect = str_replace('/david/MMCINEMA/', '', $redirect);
$redirect = ltrim($redirect, '/');

if ($serie_id <= 0) {
    header("Location: ../$redirect");
    exit();
}

try {
    $sqlCheck = "SELECT id FROM favorito_serie WHERE id_usuario = ? AND id_serie = ? LIMIT 1";
    $stmCheck = $pdo->prepare($sqlCheck);
    $stmCheck->execute([$usuario_id, $serie_id]);
    $existe = $stmCheck->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        $sqlDelete = "DELETE FROM favorito_serie WHERE id_usuario = ? AND id_serie = ?";
        $stmDelete = $pdo->prepare($sqlDelete);
        $stmDelete->execute([$usuario_id, $serie_id]);
    } else {
        $sqlInsert = "INSERT INTO favorito_serie (id_usuario, id_serie, creado) VALUES (?, ?, NOW())";
        $stmInsert = $pdo->prepare($sqlInsert);
        $stmInsert->execute([$usuario_id, $serie_id]);
    }

    header("Location: ../$redirect");
    exit();

} catch (PDOException $e) {
    header("Location: ../$redirect");
    exit();
}
?>
