<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

$id = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id <= 0) {
    header("Location: proyecciones.php?error=1");
    exit();
}

$stm = $pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_proyeccion = ?");
$stm->execute([$id]);
$count = $stm->fetchColumn();

if ($count > 0) {
    header("Location: proyecciones.php?error=tickets");
    exit();
}

$stm = $pdo->prepare("DELETE FROM ticket_asiento WHERE id_proyeccion = ?");
$stm->execute([$id]);

$sql = "DELETE FROM proyeccion WHERE id = ?";
$stm = $pdo->prepare($sql);
$stm->execute([$id]);

header("Location: proyecciones.php?ok=1");
exit();
?>
