<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM critica_serie WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: criticas_series.php");
exit;