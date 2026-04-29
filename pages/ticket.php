<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "../config/conexion.php";

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

// Poster (BD guarda "pelicula-name_XXX.webp" o solo "pelicula-name")
$posterFile = trim($ticket['poster'] ?? '');
$posterWeb  = "../assets/img/posters/" . $posterFile;
$posterAbs  = __DIR__ . "/../assets/img/posters/" . $posterFile;
$posterOk   = ($posterFile !== '' && file_exists($posterAbs));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Tu Ticket</title>
  <link rel="icon" type="image/svg+xml" href="../favicon.svg">
  <link rel="stylesheet" href="../assets/css/styles.css?v=<?= time() ?>">
  <style>
    body { font-family: Arial, sans-serif; background:#0b1220; color:#e5e7eb; margin:0; }
    .card { max-width: 820px; margin: 30px auto; background:#111827; border-radius:14px; padding:20px; }
    .row { display:flex; gap:20px; flex-wrap:wrap; }
    img { border-radius:10px; max-width:170px; height:auto; }
    .btn { display:inline-block; padding:10px 14px; border-radius:10px; text-decoration:none; background:#f59e0b; color:#111827; font-weight:bold; }
    .muted { color:#9ca3af; }
    
    .btn-volver {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background-color: #f59e0b;
      color: #000;
      font-weight: 600;
      border-radius: 10px;
      padding: 10px 14px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);
      border: none;
    }
    
    .btn-volver:hover {
      background-color: #d97706;
      color: #000;
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(245, 158, 11, 0.4);
    }
    
    .btn-volver svg {
      transition: transform 0.3s ease;
      width: 16px;
      height: 16px;
    }
    
    .btn-volver:hover svg {
      transform: translateX(-4px);
    }
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
          <img src="<?= htmlspecialchars($posterWeb) ?>" alt="Poster" style="border: 2px solid #f59e0b;">
        <?php else: ?>
          <div style="width:170px; height:255px; background:#374151; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#9ca3af; text-align:center; padding:10px; flex-direction: column;">
            <span>Sin poster disponible</span>
            <small style="margin-top: 10px; font-size: 11px; color: #6b7280;">
              Archivo: <?= htmlspecialchars($posterFile) ?><br>
              Ruta: <?= htmlspecialchars($posterWeb) ?>
            </small>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <p><b>Película:</b> <?= htmlspecialchars($ticket['titulo']) ?></p>
        <p><b>Fecha:</b> <?= htmlspecialchars($ticket['fecha']) ?> <b>Hora:</b> <?= htmlspecialchars($ticket['hora']) ?></p>
        <p><b>Sala:</b> <?= htmlspecialchars($ticket['sala']) ?></p>
        <p><b>Entradas:</b> <?= (int)$ticket['cantidad'] ?></p>
        <p><b>Asientos:</b> <?= htmlspecialchars(implode(", ", $asientos)) ?></p>
        <p><b>Total:</b> <?= number_format((float)$ticket['total'], 2) ?> €</p>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
          <a class="btn-volver" href="../pages/index.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Volver al Inicio
          </a>
          <a class="btn" href="../pages/ticket_pdf.php?id=<?= (int)$ticket['id'] ?>" target="_blank">Descargar PDF</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
