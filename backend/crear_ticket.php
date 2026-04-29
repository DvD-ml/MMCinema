<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/mail.php";
require_once __DIR__ . "/../helpers/generar_ticket_pdf.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$id_usuario = (int)$_SESSION['usuario_id'];
$proyeccion_id = isset($_POST['proyeccion_id']) ? (int)$_POST['proyeccion_id'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

$asientos_json = $_POST['asientos_json'] ?? '[]';
$asientos = json_decode($asientos_json, true);
if (!is_array($asientos)) {
    $asientos = [];
}

if ($proyeccion_id <= 0) {
    header("Location: ../pages/index.php");
    exit();
}

if ($cantidad < 1) $cantidad = 1;
if ($cantidad > 10) $cantidad = 10;

$asientos = array_values(array_unique(array_map('strval', $asientos)));
if (count($asientos) !== $cantidad) {
    die("Debes seleccionar exactamente $cantidad asiento(s).");
}

$stmUsuario = $pdo->prepare("SELECT id, username, email FROM usuario WHERE id = ?");
$stmUsuario->execute([$id_usuario]);
$usuario = $stmUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no válido.");
}

$stm = $pdo->prepare("
    SELECT pr.id, pr.fecha, pr.hora, pr.sala,
           p.titulo, p.poster
    FROM proyeccion pr
    JOIN pelicula p ON p.id = pr.id_pelicula
    WHERE pr.id = ?
");
$stm->execute([$proyeccion_id]);
$info = $stm->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    header("Location: ../pages/index.php");
    exit();
}

$stmS = $pdo->prepare("SELECT filas, columnas FROM sala_config WHERE sala = ?");
$stmS->execute([$info['sala']]);
$confSala = $stmS->fetch(PDO::FETCH_ASSOC);

$numFilas = (int)($confSala['filas'] ?? 8);
$numCols  = (int)($confSala['columnas'] ?? 10);
if ($numFilas < 1) $numFilas = 8;
if ($numCols < 1)  $numCols  = 10;

function mm_asiento_valido($a, $numFilas, $numCols)
{
    if (!preg_match('/^([A-Z])(\d{1,2})$/', $a, $m)) return false;
    $fila = ord($m[1]) - ord('A') + 1;
    $col  = (int)$m[2];
    return ($fila >= 1 && $fila <= $numFilas && $col >= 1 && $col <= $numCols);
}

foreach ($asientos as $a) {
    if (!mm_asiento_valido($a, $numFilas, $numCols)) {
        die("Asiento inválido para la sala.");
    }
}

$precio_unitario = 7.50;
$total = $precio_unitario * $cantidad;
$codigo = strtoupper(bin2hex(random_bytes(6)));

try {
    $pdo->beginTransaction();

    $ins = $pdo->prepare("
        INSERT INTO ticket (id_usuario, id_proyeccion, cantidad, precio_unitario, total, codigo)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $ins->execute([$id_usuario, $proyeccion_id, $cantidad, $precio_unitario, $total, $codigo]);

    $ticket_id = (int)$pdo->lastInsertId();

    $insA = $pdo->prepare("
        INSERT INTO ticket_asiento (id_ticket, id_proyeccion, asiento)
        VALUES (?, ?, ?)
    ");

    foreach ($asientos as $a) {
        $insA->execute([$ticket_id, $proyeccion_id, $a]);
    }

    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("No se pudo reservar. Puede que alguien haya cogido esos asientos. Vuelve y prueba otros.");
}

$asientosTxt = implode(", ", $asientos);

$datosTicketCorreo = [
    'id'       => $ticket_id,
    'codigo'   => $codigo,
    'titulo'   => $info['titulo'],
    'poster'   => $info['poster'],
    'fecha'    => $info['fecha'],
    'hora'     => $info['hora'],
    'sala'     => $info['sala'],
    'cantidad' => $cantidad,
    'asientos' => $asientosTxt,
    'total'    => number_format((float)$total, 2, '.', '')
];

$rutaPdf = generarPdfTicketGuardado($datosTicketCorreo);

$correoEnviado = enviarCorreoEntrada(
    $usuario['email'],
    $usuario['username'],
    $datosTicketCorreo,
    $rutaPdf
);

if ($correoEnviado) {
    header("Location: ../pages/ticket.php?id=" . $ticket_id . "&correo=1");
    exit();
} else {
    header("Location: ../pages/ticket.php?id=" . $ticket_id . "&correo=0");
    exit();
}