<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);

function mm_admin_delete_asset(?string $relativePath): void
{
    $relativePath = trim((string)$relativePath);
    if ($relativePath === '') {
        return;
    }

    $base = realpath(__DIR__ . "/../../../");
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
        $stmt = $pdo->prepare("SELECT poster, banner FROM serie WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $serie = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($serie) {
            mm_admin_delete_asset($serie['poster'] ?? null);
            mm_admin_delete_asset($serie['banner'] ?? null);

            $stmtTemporadas = $pdo->prepare("SELECT poster FROM temporada WHERE id_serie = ? AND poster IS NOT NULL AND poster != ''");
            $stmtTemporadas->execute([$id]);
            foreach ($stmtTemporadas->fetchAll(PDO::FETCH_COLUMN) as $posterTemporada) {
                mm_admin_delete_asset($posterTemporada);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM serie WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: list.php?deleted=1");
} catch (PDOException $e) {
    error_log("Error en series/delete.php: " . $e->getMessage());
    header("Location: list.php?error=1");
}
exit;
?>
