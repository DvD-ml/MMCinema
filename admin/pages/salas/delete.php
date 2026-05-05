<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

// Validar token CSRF
CSRF::validarOAbortar();

$sala = trim($_POST['sala'] ?? '');

if ($sala === '') {
    header("Location: list.php?error=1");
    exit();
}

// Verificar que la sala existe
$stm = $pdo->prepare("SELECT sala FROM sala_config WHERE sala = ?");
$stm->execute([$sala]);
if (!$stm->fetch()) {
    header("Location: list.php?error=1");
    exit();
}

// Verificar que no hay proyecciones usando esta sala
$stm = $pdo->prepare("SELECT COUNT(*) FROM proyeccion WHERE sala = ?");
$stm->execute([$sala]);
$count = $stm->fetchColumn();

if ($count > 0) {
    header("Location: list.php?error=proyecciones");
    exit();
}

// Eliminar sala
$sql = "DELETE FROM sala_config WHERE sala = ?";
$stm = $pdo->prepare($sql);
$stm->execute([$sala]);

header("Location: list.php?ok=1");
exit();
?>






