<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: peliculas.php");
    exit();
}

CSRF::validarOAbortar();

$id = 0;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
}

if ($id > 0) {
    $stm = $pdo->prepare("DELETE FROM pelicula WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: peliculas.php?borrado=1");
exit();
?>