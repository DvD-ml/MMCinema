<?php
require_once __DIR__ . "/../../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

// Validar CSRF
CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);
$id_temporada = (int)($_POST['id_temporada'] ?? 0);
$numero_episodio = (int)($_POST['numero_episodio'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion = (int)($_POST['duracion'] ?? 0);
$fecha_estreno = $_POST['fecha_estreno'] ?? null;

if ($id_temporada <= 0 || $numero_episodio <= 0 || $titulo === '') {
    header("Location: list.php?error=1");
    exit();
}

// Normalizar valores
$duracion = ($duracion > 0) ? $duracion : null;
$fecha_estreno = ($fecha_estreno !== '' && $fecha_estreno !== null) ? $fecha_estreno : null;

try {
    if ($id > 0) {
        // Editar
        $sql = "UPDATE episodio SET id_temporada = ?, numero_episodio = ?, titulo = ?, descripcion = ?, duracion = ?, fecha_estreno = ? WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_temporada, $numero_episodio, $titulo, $descripcion, $duracion, $fecha_estreno, $id]);
    } else {
        // Crear
        $sql = "INSERT INTO episodio (id_temporada, numero_episodio, titulo, descripcion, duracion, fecha_estreno) VALUES (?, ?, ?, ?, ?, ?)";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_temporada, $numero_episodio, $titulo, $descripcion, $duracion, $fecha_estreno]);
    }
    header("Location: list.php?id_temporada=" . $id_temporada . "&ok=1");
} catch (PDOException $e) {
    error_log("Error en series/episodios/save.php: " . $e->getMessage());
    header("Location: form.php?id=" . $id . "&error=1");
}
exit();
?>






