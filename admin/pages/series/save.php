<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../helpers/upload_helper.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar CSRF
CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$sinopsis = trim($_POST['sinopsis'] ?? '');
$fecha_estreno = $_POST['fecha_estreno'] ?? null;
$edad = trim($_POST['edad'] ?? '');
$id_genero = (int)($_POST['id_genero'] ?? 0);
$id_plataforma = (int)($_POST['id_plataforma'] ?? 0);
$estado = $_POST['estado'] ?? 'en_emision';
$destacada = isset($_POST['destacada']) ? 1 : 0;
$trailer = trim($_POST['trailer'] ?? '');

if ($titulo === '' || $sinopsis === '') {
    header("Location: list.php?error=1");
    exit();
}

// Procesar poster
$poster = $_POST['poster_actual'] ?? '';
if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
    $posterAnterior = $_POST['poster_actual'] ?? null;
    $poster = mm_upload_image($_FILES['poster_file'], 'assets/img/series/posters', 'serie_poster', $posterAnterior);
}

// Procesar banner
$banner = $_POST['banner_actual'] ?? '';
if (isset($_FILES['banner_file']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
    $bannerAnterior = $_POST['banner_actual'] ?? null;
    $banner = mm_upload_image($_FILES['banner_file'], 'assets/img/series/banners', 'serie_banner', $bannerAnterior);
}

// Normalizar valores
$fecha_estreno = ($fecha_estreno !== '' && $fecha_estreno !== null) ? $fecha_estreno : null;
$trailer = ($trailer !== '') ? $trailer : null;

try {
    if ($id > 0) {
        // Editar
        $sql = "UPDATE serie SET titulo = ?, sinopsis = ?, poster = ?, banner = ?, fecha_estreno = ?, edad = ?, id_genero = ?, id_plataforma = ?, estado = ?, destacada = ?, trailer = ? WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$titulo, $sinopsis, $poster, $banner, $fecha_estreno, $edad, $id_genero, $id_plataforma, $estado, $destacada, $trailer, $id]);
    } else {
        // Crear
        $sql = "INSERT INTO serie (titulo, sinopsis, poster, banner, fecha_estreno, edad, id_genero, id_plataforma, estado, destacada, trailer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stm = $pdo->prepare($sql);
        $stm->execute([$titulo, $sinopsis, $poster, $banner, $fecha_estreno, $edad, $id_genero, $id_plataforma, $estado, $destacada, $trailer]);
    }
    header("Location: list.php?ok=1");
} catch (PDOException $e) {
    error_log("Error en series/save.php: " . $e->getMessage());
    header("Location: form.php?id=" . $id . "&error=1");
}
exit();
?>






