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
if (isset($_GET["id"])) {
    $id = (int)$_GET["id"];
}

$idSerie = 0;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT id_serie FROM temporada WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $temp = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($temp) {
        $idSerie = (int)$temp['id_serie'];
    }

    $stmtDelete = $pdo->prepare("DELETE FROM temporada WHERE id = ?");
    $stmtDelete->execute([$id]);
}

header("Location: temporadas.php" . ($idSerie > 0 ? "?id_serie=" . $idSerie : ""));
exit;