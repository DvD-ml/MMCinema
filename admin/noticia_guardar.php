<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../includes/optimizar_imagen.php";
require_once "../helpers/CSRF.php";
require_once "../helpers/FileValidation.php";


// Validar token CSRF
CSRF::validarOAbortar();

$id           = (int)($_POST['id'] ?? 0);
$titulo       = trim($_POST['titulo'] ?? '');
$contenido    = trim($_POST['contenido'] ?? '');
$imagenActual = trim($_POST['imagen_actual'] ?? '');
$imagen       = $imagenActual;

if ($titulo === '' || $contenido === '') {
    header("Location: noticias.php?error=1");
    exit();
}

if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
    try {
        $imagen = optimizarYGuardarWebp(
            $_FILES['imagen_file'],
            __DIR__ . '/../assets/img/noticias',
            'noticia_' . $titulo,
            72,
            1400,
            900,
            $imagenActual !== '' ? $imagenActual : null
        );
    } catch (Throwable $e) {
        header("Location: noticias.php?error=imagen");
        exit();
    }
}

if ($id > 0) {
    $sql = "UPDATE noticia
            SET titulo = ?, contenido = ?, imagen = ?
            WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$titulo, $contenido, $imagen, $id]);
} else {
    $sql = "INSERT INTO noticia (titulo, contenido, imagen, publicado)
            VALUES (?, ?, ?, NOW())";
    $stm = $pdo->prepare($sql);
    $stm->execute([$titulo, $contenido, $imagen]);
}

header("Location: noticias.php?ok=1");
exit();
?>