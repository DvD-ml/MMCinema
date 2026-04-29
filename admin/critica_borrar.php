<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

$id = 0;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
}

if ($id > 0) {
    $stm = $pdo->prepare("DELETE FROM critica WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: criticas.php?borrado=1");
exit();
?>