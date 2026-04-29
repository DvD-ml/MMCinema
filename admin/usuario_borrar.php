<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

$id = 0;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
}

if ($id > 0 && $id !== (int)$_SESSION['usuario_id']) {
    $stm = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: usuarios.php?borrado=1");
exit();
?>