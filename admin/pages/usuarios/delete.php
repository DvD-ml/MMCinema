<?php
require_once "../../../auth.php";
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);

// No permitir eliminar el usuario actual
if ($id > 0 && $id !== (int)$_SESSION['usuario_id']) {
    $stm = $pdo->prepare("DELETE FROM usuario WHERE id = ?");
    $stm->execute([$id]);
}

header("Location: list.php?borrado=1");
exit();
?>





