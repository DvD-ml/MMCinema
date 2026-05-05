<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

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
$idSerie = 0;

try {
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
    header("Location: list.php" . ($idSerie > 0 ? "?id_serie=" . $idSerie : ""));
} catch (PDOException $e) {
    error_log("Error en series/temporadas/delete.php: " . $e->getMessage());
    header("Location: list.php?error=1");
}
exit;
?>






