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

// Validar token CSRF
CSRF::validarOAbortar();

if (empty($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$idTemporada = 0;

try {
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT id_temporada FROM episodio WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $ep = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ep) $idTemporada = (int)$ep['id_temporada'];

        $stmtDelete = $pdo->prepare("DELETE FROM episodio WHERE id = ?");
        $stmtDelete->execute([$id]);
    }
    header("Location: list.php" . ($idTemporada > 0 ? "?id_temporada=" . $idTemporada : ""));
} catch (PDOException $e) {
    error_log("Error en series/episodios/delete.php: " . $e->getMessage());
    header("Location: list.php?error=1");
}
exit;
?>






