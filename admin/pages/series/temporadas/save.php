<?php
require_once "../../../../auth.php";
verificarAuth();

require_once __DIR__ . "/../../../../config/conexion.php";
require_once __DIR__ . "/../../../../helpers/upload_helper.php";
require_once __DIR__ . "/../../../../helpers/CSRF.php";

$id = (int)($_POST['id'] ?? 0);
$id_serie = (int)($_POST['id_serie'] ?? 0);
$numero_temporada = (int)($_POST['numero_temporada'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha_estreno = $_POST['fecha_estreno'] ?? null;

if ($id_serie <= 0 || $numero_temporada <= 0) {
    header("Location: list.php?error=1");
    exit();
}

// Procesar poster
$poster = $_POST['poster_actual'] ?? '';
if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
    $posterAnterior = $_POST['poster_actual'] ?? null;
    $poster = mm_upload_image($_FILES['poster_file'], 'assets/img/series/temporadas', 'temporada_poster', $posterAnterior);
}

// Normalizar valores
$fecha_estreno = ($fecha_estreno !== '' && $fecha_estreno !== null) ? $fecha_estreno : null;

if ($id > 0) {
    // Editar
    $sql = "UPDATE temporada SET id_serie = ?, numero_temporada = ?, titulo = ?, descripcion = ?, poster = ?, fecha_estreno = ? WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id_serie, $numero_temporada, $titulo, $descripcion, $poster, $fecha_estreno, $id]);
} else {
    // Crear
    $sql = "INSERT INTO temporada (id_serie, numero_temporada, titulo, descripcion, poster, fecha_estreno) VALUES (?, ?, ?, ?, ?, ?)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id_serie, $numero_temporada, $titulo, $descripcion, $poster, $fecha_estreno]);
}

header("Location: list.php?id_serie=" . $id_serie . "&ok=1");
exit();
?>






