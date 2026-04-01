<?php
session_start();
require_once("../config/conexion.php");

if (empty($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$id_usuario = (int)$_SESSION['usuario_id'];
$id_serie = isset($_POST['id_serie']) ? (int)$_POST['id_serie'] : 0;
$puntuacion = isset($_POST['puntuacion']) ? (int)$_POST['puntuacion'] : 0;
$contenido = trim($_POST['contenido'] ?? '');

if ($id_serie <= 0 || $puntuacion < 1 || $puntuacion > 5 || $contenido === '') {
    header("Location: ../serie.php?id=" . $id_serie);
    exit;
}

$stmtCheck = $pdo->prepare("
    SELECT id
    FROM critica_serie
    WHERE id_usuario = ? AND id_serie = ?
    LIMIT 1
");
$stmtCheck->execute([$id_usuario, $id_serie]);
$existe = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $stmtUpdate = $pdo->prepare("
        UPDATE critica_serie
        SET contenido = ?, puntuacion = ?, creado = NOW()
        WHERE id = ?
    ");
    $stmtUpdate->execute([$contenido, $puntuacion, $existe['id']]);
} else {
    $stmtInsert = $pdo->prepare("
        INSERT INTO critica_serie (id_usuario, id_serie, contenido, puntuacion)
        VALUES (?, ?, ?, ?)
    ");
    $stmtInsert->execute([$id_usuario, $id_serie, $contenido, $puntuacion]);
}

header("Location: ../serie.php?id=" . $id_serie . "#criticas-series");
exit;