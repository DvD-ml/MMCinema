<?php
session_start();
require_once "../config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../login.php");
  exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];

// Acepta ambos nombres por compatibilidad (por si alguna vista antigua sigue enviando estos campos)
$pelicula_id = (int)($_POST['pelicula_id'] ?? ($_POST['id_pelicula'] ?? 0));
$contenido   = trim($_POST['contenido'] ?? ($_POST['texto'] ?? ""));

// La puntuación es opcional. Si se envía, debe ser 1..5; si no, guardamos NULL.
$puntuacion_raw = $_POST['puntuacion'] ?? "";
$puntuacion = null;
if ($puntuacion_raw !== "") {
  $p = (int)$puntuacion_raw;
  if ($p >= 1 && $p <= 5) {
    $puntuacion = $p;
  }
}

// Contenido obligatorio; película válida obligatoria.
if ($pelicula_id <= 0 || $contenido === "") {
  header("Location: ../pelicula.php?id=".$pelicula_id);
  exit();
}

$stm = $pdo->prepare("INSERT INTO critica (id_usuario, id_pelicula, contenido, puntuacion) VALUES (?, ?, ?, ?)");
$stm->execute([$usuario_id, $pelicula_id, $contenido, $puntuacion]);

header("Location: ../pelicula.php?id=".$pelicula_id);
exit();
