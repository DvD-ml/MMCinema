<?php
require_once "auth.php";
require_once "../config/conexion.php";

$sala = $_GET['sala'] ?? '';

if ($sala === '') {
    header("Location: salas.php?error=1");
    exit();
}

// Verificar si hay proyecciones en esta sala
$stm = $pdo->prepare("SELECT COUNT(*) FROM proyeccion WHERE sala = ?");
$stm->execute([$sala]);
$count = $stm->fetchColumn();

if ($count > 0) {
    header("Location: salas.php?error=proyecciones");
    exit();
}

// Eliminar la sala
$sql = "DELETE FROM sala_config WHERE sala = ?";
$stm = $pdo->prepare($sql);
$stm->execute([$sala]);

header("Location: salas.php?ok=1");
exit();
?>
