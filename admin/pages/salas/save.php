<?php
require_once "../../../auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$sala = trim($_POST['sala'] ?? '');
$salaAnterior = trim($_POST['sala_anterior'] ?? '');
$filas = (int)($_POST['filas'] ?? 0);
$columnas = (int)($_POST['columnas'] ?? 0);

if ($sala === '' || $filas <= 0 || $columnas <= 0) {
    header("Location: list.php?error=1");
    exit();
}

if ($salaAnterior === '') {
    // Crear nueva sala - verificar que no existe
    $stm = $pdo->prepare("SELECT COUNT(*) FROM sala_config WHERE sala = ?");
    $stm->execute([$sala]);
    if ($stm->fetchColumn() > 0) {
        header("Location: list.php?error=duplicado");
        exit();
    }
    
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

header("Location: list.php?ok=1");
exit();
?>






