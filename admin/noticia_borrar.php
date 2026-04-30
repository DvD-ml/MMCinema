<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: noticias.php");
    exit();
}

CSRF::validarOAbortar();

$id = 0;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
}

if ($id > 0) {
    $stm = $pdo->prepare("DELETE FROM noticia WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: noticias.php?borrado=1");
exit();
?>