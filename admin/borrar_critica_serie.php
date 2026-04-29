<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once "../helpers/CSRF.php";

CSRF::validarOAbortar();

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = 0;
if (isset($_POST["id"])) {
    $id = (int)$_POST["id"];
}

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM critica_serie WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: criticas_series.php");
exit;
?>