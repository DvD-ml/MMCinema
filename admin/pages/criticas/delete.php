<?php
require_once "../../../auth.php";
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

CSRF::validarOAbortar();

$tipo = $_POST['tipo'] ?? 'pelicula';
if (!in_array($tipo, ['pelicula', 'serie'], true)) {
    $tipo = 'pelicula';
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    if ($tipo === 'pelicula') {
        $stm = $pdo->prepare("DELETE FROM critica WHERE id = ?");
    } else {
        $stm = $pdo->prepare("DELETE FROM critica_serie WHERE id = ?");
    }
    $stm->execute([$id]);
}

header("Location: list.php?borrado=1");
exit();
?>






