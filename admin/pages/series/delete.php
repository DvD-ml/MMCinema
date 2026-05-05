<?php
require_once "../../../auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

CSRF::validarOAbortar();

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM serie WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: list.php");
exit;
?>






