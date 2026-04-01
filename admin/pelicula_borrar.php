<?php
require_once "auth.php";
require_once "../config/conexion.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stm = $pdo->prepare("DELETE FROM pelicula WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: peliculas.php?borrado=1");
exit();
?>