<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$sala = trim($_POST['sala'] ?? '');
$salaAnterior = trim($_POST['sala_anterior'] ?? '');
$filas = (int)($_POST['filas'] ?? 0);
$columnas = (int)($_POST['columnas'] ?? 0);

if ($sala === '' || $filas <= 0 || $columnas <= 0) {
    header("Location: salas.php?error=1");
    exit();
}

if ($salaAnterior === '') {
    // Crear nueva sala
    $sql = "INSERT INTO sala_config (sala, filas, columnas) VALUES (?, ?, ?)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$sala, $filas, $columnas]);
} else {
    // Editar sala existente
    $sql = "UPDATE sala_config SET filas = ?, columnas = ? WHERE sala = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$filas, $columnas, $salaAnterior]);
}

header("Location: salas.php?ok=1");
exit();
?>
