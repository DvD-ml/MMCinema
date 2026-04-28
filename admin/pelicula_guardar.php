<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../includes/optimizar_imagen.php";

$id           = (int)($_POST['id'] ?? 0);
$titulo       = trim($_POST['titulo'] ?? '');
$sinopsis     = trim($_POST['sinopsis'] ?? '');
$posterActual = trim($_POST['poster_actual'] ?? '');
$fechaEstreno = $_POST['fecha_estreno'] ?? '';
$duracion     = (int)($_POST['duracion'] ?? 0);
$edad         = trim($_POST['edad'] ?? '');
$idGenero     = (int)($_POST['id_genero'] ?? 0);
$trailer      = trim($_POST['trailer'] ?? '');
$poster       = $posterActual;

if (
    $titulo === '' || $sinopsis === '' || $fechaEstreno === '' ||
    $duracion <= 0 || $edad === '' || $idGenero <= 0
) {
    header("Location: peliculas.php?error=1");
    exit();
}

if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
    try {
        $poster = optimizarYGuardarWebp(
            $_FILES['poster_file'],
            __DIR__ . '/../assets/img/posters',
            'pelicula_' . $titulo,
            70,
            900,
            1350,
            $posterActual !== '' ? $posterActual : null
        );
    } catch (Throwable $e) {
        header("Location: peliculas.php?error=imagen");
        exit();
    }
}

if ($id > 0) {
    $sql = "UPDATE pelicula
            SET titulo = ?, sinopsis = ?, poster = ?, fecha_estreno = ?, duracion = ?, edad = ?, id_genero = ?, trailer = ?
            WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$titulo, $sinopsis, $poster, $fechaEstreno, $duracion, $edad, $idGenero, $trailer, $id]);
} else {
    $sql = "INSERT INTO pelicula (titulo, sinopsis, poster, fecha_estreno, duracion, edad, id_genero, trailer)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$titulo, $sinopsis, $poster, $fechaEstreno, $duracion, $edad, $idGenero, $trailer]);
}

header("Location: peliculas.php?ok=1");
exit();
?>