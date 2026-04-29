<?php
require_once "auth.php";
require_once "../config/conexion.php";
require_once "../helpers/CSRF.php";

// Validar token CSRF
CSRF::validarOAbortar();

$tipo = $_POST['tipo'] ?? 'pelicula';
if (!in_array($tipo, ['pelicula', 'serie'], true)) {
    $tipo = 'pelicula';
}

$id = (int)($_POST['id'] ?? 0);
$id_usuario = (int)($_POST['id_usuario'] ?? 0);
$id_objeto = (int)($_POST['id_objeto'] ?? 0);
$puntuacion = (int)($_POST['puntuacion'] ?? 5);
$contenido = trim($_POST['contenido'] ?? '');

if ($id_usuario <= 0 || $id_objeto <= 0 || $puntuacion < 1 || $puntuacion > 5 || $contenido === '') {
    header("Location: criticas.php?error=1");
    exit();
}

if ($tipo === 'pelicula') {
    if ($id > 0) {
        $sql = "UPDATE critica
                SET id_usuario = ?, id_pelicula = ?, puntuacion = ?, contenido = ?
                WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_usuario, $id_objeto, $puntuacion, $contenido, $id]);
    } else {
        $sql = "INSERT INTO critica (id_usuario, id_pelicula, puntuacion, contenido, creado)
                VALUES (?, ?, ?, ?, NOW())";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_usuario, $id_objeto, $puntuacion, $contenido]);
    }
} else {
    if ($id > 0) {
        $sql = "UPDATE critica_serie
                SET id_usuario = ?, id_serie = ?, puntuacion = ?, contenido = ?
                WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_usuario, $id_objeto, $puntuacion, $contenido, $id]);
    } else {
        $sql = "INSERT INTO critica_serie (id_usuario, id_serie, puntuacion, contenido, creado)
                VALUES (?, ?, ?, ?, NOW())";
        $stm = $pdo->prepare($sql);
        $stm->execute([$id_usuario, $id_objeto, $puntuacion, $contenido]);
    }
}

header("Location: criticas.php?ok=1");
exit();
?>
