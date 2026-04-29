<?php
require_once "auth.php";
verificarAuth();

require_once("../config/conexion.php");
require_once "../helpers/CSRF.php";


// Validar token CSRF
CSRF::validarOAbortar();

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$idTemporada = 0;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT id_temporada FROM episodio WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $ep = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($ep) $idTemporada = (int)$ep['id_temporada'];

    $stmtDelete = $pdo->prepare("DELETE FROM episodio WHERE id = ?");
    $stmtDelete->execute([$id]);
}

header("Location: episodios.php" . ($idTemporada > 0 ? "?id_temporada=" . $idTemporada : ""));
exit;