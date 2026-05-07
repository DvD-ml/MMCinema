<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);
$idSerie = 0;

function mm_admin_delete_season_asset(?string $relativePath): void
{
    $relativePath = trim((string)$relativePath);
    if ($relativePath === '') {
        return;
    }

    $base = realpath(__DIR__ . "/../../../../");
    if ($base === false) {
        return;
    }

    $absolutePath = realpath($base . DIRECTORY_SEPARATOR . ltrim(str_replace('\\', '/', $relativePath), '/'));
    if ($absolutePath !== false && str_starts_with($absolutePath, $base . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img') && is_file($absolutePath)) {
        @unlink($absolutePath);
    }
}

try {
    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT id_serie, poster FROM temporada WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $temp = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($temp) {
            $idSerie = (int)$temp['id_serie'];
            mm_admin_delete_season_asset($temp['poster'] ?? null);
        }

        $stmtDelete = $pdo->prepare("DELETE FROM temporada WHERE id = ?");
        $stmtDelete->execute([$id]);
    }
    header("Location: list.php" . ($idSerie > 0 ? "?id_serie=" . $idSerie . "&deleted=1" : "?deleted=1"));
} catch (PDOException $e) {
    error_log("Error en series/temporadas/delete.php: " . $e->getMessage());
    header("Location: list.php?error=1");
}
exit;
?>
