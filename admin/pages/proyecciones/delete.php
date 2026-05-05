<?php
require_once "../../../auth.php";
require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: list.php?error=1");
    exit();
}

// Obtener el id_pelicula antes de eliminar
$stm = $pdo->prepare("SELECT id_pelicula FROM proyeccion WHERE id = ?");
$stm->execute([$id]);
$proyeccion = $stm->fetch(PDO::FETCH_ASSOC);

if (!$proyeccion) {
    header("Location: list.php?error=1");
    exit();
}

$id_pelicula = $proyeccion['id_pelicula'];

// Verificar dependencias (tickets)
$stm = $pdo->prepare("SELECT COUNT(*) FROM ticket WHERE id_proyeccion = ?");
$stm->execute([$id]);
$count = $stm->fetchColumn();

if ($count > 0) {
    header("Location: form.php?pelicula_id=" . $id_pelicula . "&error=tickets");
    exit();
}

// Eliminar asientos asociados
$stm = $pdo->prepare("DELETE FROM ticket_asiento WHERE id_proyeccion = ?");
$stm->execute([$id]);

// Eliminar proyección
$sql = "DELETE FROM proyeccion WHERE id = ?";
$stm = $pdo->prepare($sql);
$stm->execute([$id]);

header("Location: form.php?pelicula_id=" . $id_pelicula . "&ok=1");
exit();
?>






