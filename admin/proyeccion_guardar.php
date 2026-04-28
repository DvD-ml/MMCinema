<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);
$id_pelicula = (int)($_POST['id_pelicula'] ?? 0);
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';
$sala = trim($_POST['sala'] ?? '');

if ($id_pelicula <= 0 || $fecha === '' || $hora === '' || $sala === '') {
    header("Location: proyecciones.php?error=1");
    exit();
}

// Validar que la película existe
$stm = $pdo->prepare("SELECT id FROM pelicula WHERE id = ?");
$stm->execute([$id_pelicula]);
if (!$stm->fetch()) {
    header("Location: proyecciones.php?error=1");
    exit();
}

// Validar que la sala existe
$stm = $pdo->prepare("SELECT sala FROM sala_config WHERE sala = ?");
$stm->execute([$sala]);
if (!$stm->fetch()) {
    header("Location: proyecciones.php?error=1");
    exit();
}

if ($id > 0) {
    // Editar proyección existente
    $sql = "UPDATE proyeccion SET id_pelicula = ?, fecha = ?, hora = ?, sala = ? WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id_pelicula, $fecha, $hora, $sala, $id]);
} else {
    // Crear nueva proyección
    $sql = "INSERT INTO proyeccion (id_pelicula, fecha, hora, sala) VALUES (?, ?, ?, ?)";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id_pelicula, $fecha, $hora, $sala]);
}

header("Location: proyecciones.php?ok=1");
exit();
?>
