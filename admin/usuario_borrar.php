<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && $id !== (int)$_SESSION['usuario_id']) {
    $stm = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: usuarios.php?borrado=1");
exit();
?>