<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

$id_usuario = (int)$_SESSION['usuario_id'];
$ticket_id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($ticket_id <= 0) die("Ticket no válido.");

$stm = $pdo->prepare("
  SELECT t.id, t.codigo, t.cantidad, t.precio_unitario, t.total, t.created_at,
         pr.fecha, pr.hora, pr.sala,
         p.titulo, p.poster
  FROM ticket t
  JOIN proyeccion pr ON pr.id = t.id_proyeccion
  JOIN pelicula p ON p.id = pr.id_pelicula
  WHERE t.id = ? AND t.id_usuario = ?
");
$stm->execute([$ticket_id, $id_usuario]);
$ticket = $stm->fetch(PDO::FETCH_ASSOC);

if (!$ticket) die("Ticket no válido.");

// Asientos del ticket
$stm2 = $pdo->prepare("SELECT asiento FROM ticket_asiento WHERE id_ticket = ? ORDER BY asiento ASC");
$stm2->execute([$ticket_id]);
$asientos = $stm2->fetchAll(PDO::FETCH_COLUMN);

// Poster (BD guarda "4.jpg")
$posterFile = trim($ticket['poster'] ?? '');
$posterWeb  = "assets/img/posters/" . $posterFile;
$posterAbs  = __DIR__ . "/" . $posterWeb;
$posterOk   = ($posterFile !== '' && file_exists($posterAbs));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu Ticket</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; background:#0b1220; color:#e5e7eb; margin:0; }
    .card { max-width: 820px; margin: 30px auto; background:#111827; border-radius:14px; padding:20px; }
    .row { display:flex; gap:20px; flex-wrap:wrap; }
    img { border-radius:10px; max-width:170px; height:auto; }
    .btn { display:inline-block; padding:10px 14px; border-radius:10px; text-decoration:none; background:#f59e0b; color:#111827; font-weight:bold; }
    .muted { color:#9ca3af; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Ticket confirmado</h2>
    <?php if (isset($_GET['correo']) && $_GET['correo'] === '1'): ?>
      <p style="background:#14532d; color:#dcfce7; padding:10px 12px; border-radius:10px;">
        Te hemos enviado un correo con la información de la reserva y el PDF adjunto.
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['correo']) && $_GET['correo'] === '0'): ?>
      <p style="background:#78350f; color:#fef3c7; padding:10px 12px; border-radius:10px;">
        El ticket se ha generado correctamente, pero no se pudo enviar el correo.
      </p>
    <?php endif; ?>
    
    <p class="muted">Código: <b><?= htmlspecialchars($ticket['codigo']) ?></b></p>

    <div class="qr-box">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($ticket['codigo']) ?>" alt="QR del ticket">
      <div>
        <div class="muted" style="margin-bottom:6px;">Escanea el QR para comprobar el código.</div>
        <div style="font-weight:800;"><?= htmlspecialchars($ticket['codigo']) ?></div>
      </div>
    </div>


    <div class="row">
      <div>
        <?php if ($posterOk): ?>
          <img src="<?= htmlspecialchars($posterWeb) ?>" alt="Poster">
        <?php endif; ?>
      </div>

      <div>
        <p><b>Película:</b> <?= htmlspecialchars($ticket['titulo']) ?></p>
        <p><b>Fecha:</b> <?= htmlspecialchars($ticket['fecha']) ?> <b>Hora:</b> <?= htmlspecialchars($ticket['hora']) ?></p>
        <p><b>Sala:</b> <?= htmlspecialchars($ticket['sala']) ?></p>
        <p><b>Entradas:</b> <?= (int)$ticket['cantidad'] ?></p>
        <p><b>Asientos:</b> <?= htmlspecialchars(implode(", ", $asientos)) ?></p>
        <p><b>Total:</b> <?= number_format((float)$ticket['total'], 2) ?> €</p>

        <p style="margin-top:16px;">
          <a class="btn" href="ticket_pdf.php?id=<?= (int)$ticket['id'] ?>" target="_blank">Descargar PDF</a>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
