<?php
session_start();
require_once "../config/conexion.php";

if (empty($_SESSION['usuario_id'])) {
  header("Location: ../login.php");
  exit();
}

$id_usuario = (int)$_SESSION['usuario_id'];
$id_proyeccion = (int)($_POST['id_proyeccion'] ?? 0);
$id_pelicula = (int)($_POST['id_pelicula'] ?? 0);
$asientos = trim($_POST['asientos'] ?? '');

if ($id_proyeccion <= 0 || $id_pelicula <= 0 || $asientos === '') {
  header("Location: ../index.php");
  exit();
}

$stm = $pdo->prepare("INSERT INTO reserva (id_usuario, id_proyeccion, asientos, fecha_reserva) VALUES (?, ?, ?, NOW())");

try {
  $stm->execute([$id_usuario, $id_proyeccion, $asientos]);
  $reserva_id = (int)$pdo->lastInsertId();

  // Creamos ticket automáticamente
  header("Location: ../pages/crear_ticket.php?reserva_id=" . $reserva_id);
  exit();
} catch (PDOException $e) {
  header("Location: ../pages/pelicula.php?id=" . $id_pelicula . "&reserva=error");
  exit();
}


