<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: proyecciones.php?error=1");
    exit();
}

// Verificar si hay tickets vendidos para esta proyección
$stm = $pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_proyeccion = ?");
$stm->execute([$id]);
$count = $stm->fetchColumn();

if ($count > 0) {
    header("Location: proyecciones.php?error=tickets");
    exit();
}

// Eliminar asientos asociados (si los hay)
$stm = $pdo->prepare("DELETE FROM ticket_asiento WHERE id_proyeccion = ?");
$stm->execute([$id]);

// Eliminar la proyección
$sql = "DELETE FROM proyeccion WHERE id = ?";
$stm = $pdo->prepare($sql);
$stm->execute([$id]);

header("Location: proyecciones.php?ok=1");
exit();
?>
