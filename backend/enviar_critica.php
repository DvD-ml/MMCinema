<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../pages/login.php");
  exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];

$pelicula_id = 0;
$contenido = '';
$puntuacion = null;

if (isset($_POST['pelicula_id'])) {
  $pelicula_id = (int)$_POST['pelicula_id'];
}

if (isset($_POST['id_pelicula'])) {
  $pelicula_id = (int)$_POST['id_pelicula'];
}

if (isset($_POST['contenido'])) {
  $contenido = trim($_POST['contenido']);
}

if (isset($_POST['texto'])) {
  $contenido = trim($_POST['texto']);
}

if (isset($_POST['puntuacion'])) {
  $puntuacion_raw = $_POST['puntuacion'];
  if ($puntuacion_raw !== '') {
    $p = (int)$puntuacion_raw;
    if ($p >= 1 && $p <= 5) {
      $puntuacion = $p;
    }
  }
}

if ($pelicula_id <= 0 || $contenido === '') {
  header("Location: ../pages/pelicula.php?id=" . $pelicula_id);
  exit();
}

$sqlCheck = "SELECT id FROM critica WHERE id_usuario = ? AND id_pelicula = ? LIMIT 1";
$stmCheck = $pdo->prepare($sqlCheck);
$stmCheck->execute([$usuario_id, $pelicula_id]);
$existe = $stmCheck->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $sqlUpdate = "UPDATE critica SET contenido = ?, puntuacion = ?, creado = NOW() WHERE id = ?";
    $stmUpdate = $pdo->prepare($sqlUpdate);
    $stmUpdate->execute([$contenido, $puntuacion, $existe['id']]);
} else {
    $sqlInsert = "INSERT INTO critica (id_usuario, id_pelicula, contenido, puntuacion) VALUES (?, ?, ?, ?)";
    $stm = $pdo->prepare($sqlInsert);
    $stm->execute([$usuario_id, $pelicula_id, $contenido, $puntuacion]);
}

header("Location: ../pages/pelicula.php?id=" . $pelicula_id);
exit();
?>
