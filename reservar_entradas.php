<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/config/conexion.php";

// Login obligatorio
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

// Proyección
$proyeccion_id = isset($_GET['proyeccion_id']) ? (int)$_GET['proyeccion_id'] : 0;
if ($proyeccion_id <= 0) {
  header("Location: index.php");
  exit();
}

// Proyección + película (SIN precio)
$stm = $pdo->prepare("
  SELECT pr.id AS proyeccion_id, pr.fecha, pr.hora, pr.sala,
         p.id AS pelicula_id, p.titulo, p.poster
  FROM proyeccion pr
  JOIN pelicula p ON p.id = pr.id_pelicula
  WHERE pr.id = ?
");
$stm->execute([$proyeccion_id]);
$info = $stm->fetch(PDO::FETCH_ASSOC);
if (!$info) die("Proyección no válida.");

// Precio fijo
$precio_unitario = 7.50;

// Asientos reservados
$stm2 = $pdo->prepare("SELECT asiento FROM ticket_asiento WHERE id_proyeccion = ?");
$stm2->execute([$proyeccion_id]);
$reservados = $stm2->fetchAll(PDO::FETCH_COLUMN);
$resSet = [];
foreach ($reservados as $a) $resSet[$a] = true;


// Config sala (configurable en BD)
$stmS = $pdo->prepare("SELECT filas, columnas FROM sala_config WHERE sala = ?");
$stmS->execute([$info['sala']]);
$confSala = $stmS->fetch(PDO::FETCH_ASSOC);

$numFilas = (int)($confSala['filas'] ?? 8);
$numCols  = (int)($confSala['columnas'] ?? 10);
if ($numFilas < 1) $numFilas = 8;
if ($numCols < 1)  $numCols  = 10;

// Creamos arrays de filas (A,B,C...) y columnas (1..N)
$FILAS = [];
for ($i=0; $i<$numFilas; $i++){
  $FILAS[] = chr(ord('A') + $i);
}
$COLS = range(1, $numCols);

// Poster: en la BD guardas SOLO nombre (ej: 4.jpg)
$posterFile = trim($info['poster'] ?? '');
$posterWeb  = "assets/posters/" . $posterFile; // ruta web
$posterAbs  = __DIR__ . "/" . $posterWeb;      // ruta real
$posterOk   = ($posterFile !== '' && file_exists($posterAbs));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reservar entradas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { margin:0; font-family: Arial, sans-serif; background:#0b1220; color:#e5e7eb; }
    .wrap { max-width: 1100px; margin: 0 auto; padding: 20px; }
    h1 { margin: 10px 0 18px; text-align:center; }

    .layout { display:grid; grid-template-columns: 340px 1fr; gap:18px; }
    @media (max-width: 980px){ .layout{ grid-template-columns:1fr; } }

    .card { background:#111827; border-radius:18px; padding:18px; box-shadow: 0 12px 30px rgba(0,0,0,.25); }
    .muted { color:#9ca3af; }
    .poster { border-radius:16px; overflow:hidden; background:#0f172a; }
    .poster img { width:100%; display:block; }
    .poster .empty { padding:18px; }

    .badgeRow { display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; }
    .badge { display:inline-flex; gap:8px; align-items:center; padding:8px 10px; border-radius:999px; background:#0f172a; border:1px solid #1f2937; color:#cbd5e1; font-size:13px; }

    .price { font-size:22px; font-weight:bold; margin-top:14px; }
    .box { margin-top:14px; background:#0f172a; border:1px solid #1f2937; border-radius:16px; padding:14px; }

    label { display:block; margin-top:10px; margin-bottom:6px; font-weight:bold; }
    input[type="number"] {
      width:120px; padding:10px; border-radius:12px;
      border:1px solid #334155; background:#0b1220; color:#e5e7eb;
    }

    .actions { display:flex; gap:10px; align-items:center; margin-top:14px; flex-wrap:wrap; }
    .btn {
      border:0; padding:12px 14px; border-radius:12px;
      background:#f59e0b; color:#111827; font-weight:bold; cursor:pointer;
    }
    .btn:disabled { opacity:.6; cursor:not-allowed; }

    /* ✅ ÚNICO ENLACE */
    .backLink {
      color:#e5e7eb; text-decoration:none; padding:12px 14px; border-radius:12px;
      background:#1f2937; border:1px solid #334155; display:inline-block;
    }
    .backLink:hover { filter:brightness(1.1); }

    /* Asientos */
    .screen {
      text-align:center; padding:10px; border-radius:999px;
      background: linear-gradient(90deg, rgba(245,158,11,.15), rgba(245,158,11,.35), rgba(245,158,11,.15));
      border:1px solid rgba(245,158,11,.45);
      margin-bottom:14px;
      color:#fde68a;
      font-weight:bold;
      letter-spacing:.6px;
    }

    .legend { display:flex; gap:10px; flex-wrap:wrap; margin: 10px 0; }
    .pill { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:999px; background:#0b1220; border:1px solid #1f2937; color:#cbd5e1; font-size:13px; }
    .dot { width:12px; height:12px; border-radius:4px; display:inline-block; }
    .dot.free { background:#22c55e; }
    .dot.sel { background:#f59e0b; }
    .dot.busy { background:#64748b; }

    .seats { display:grid; gap:8px; justify-content:center; }
    .seatRow { display:flex; gap:8px; align-items:center; }
    .rowLabel { width:18px; text-align:center; color:#94a3b8; font-weight:bold; }

    .seat {
      width:34px; height:34px; border-radius:10px;
      border:1px solid #334155;
      background:#0b1220;
      color:#cbd5e1;
      cursor:pointer;
      font-size:12px;
    }
    .seat.busy { background:#111827; color:#64748b; cursor:not-allowed; opacity:.8; }
    .seat.selected { background:#f59e0b; border-color:#f59e0b; color:#111827; font-weight:bold; }

    .countBox { margin-top:8px; padding:10px; background:#0b1220; border:1px solid #1f2937; border-radius:14px; }
  </style>
</head>
<body>

<!-- ✅ SIN NAVBAR EN ESTA PÁGINA -->

<div class="wrap">
  <h1>Reservar entradas</h1>

  <div class="layout">
    <!-- Izquierda -->
    <div class="card">
      <div class="poster">
        <?php if ($posterOk): ?>
          <img src="<?= htmlspecialchars($posterWeb) ?>" alt="Poster">
        <?php else: ?>
          <div class="empty muted">Sin poster disponible</div>
        <?php endif; ?>
      </div>

      <h2 style="margin:14px 0 6px;"><?= htmlspecialchars($info['titulo']) ?></h2>
      <div class="muted">Elige la cantidad y selecciona los asientos.</div>

      <div class="badgeRow">
        <div class="badge">📅 <?= htmlspecialchars($info['fecha']) ?></div>
        <div class="badge">🕒 <?= htmlspecialchars($info['hora']) ?></div>
        <div class="badge">🎬 <?= htmlspecialchars($info['sala']) ?></div>
      </div>

      <div class="price"><?= number_format($precio_unitario, 2) ?> € <span class="muted" style="font-size:14px;">/ entrada</span></div>
    </div>

    <!-- Derecha -->
    <div class="card">

      <div class="legend">
        <div class="pill"><span class="dot free"></span> Libre</div>
        <div class="pill"><span class="dot sel"></span> Seleccionado</div>
        <div class="pill"><span class="dot busy"></span> Reservado</div>
      </div>

      <form id="formReserva" action="backend/crear_ticket.php" method="POST">
        <input type="hidden" name="proyeccion_id" value="<?= (int)$info['proyeccion_id'] ?>">
        <input type="hidden" name="asientos_json" id="asientos_json" value="[]">

        <div class="box">
          <label for="cantidad">Cantidad</label>
          <input id="cantidad" type="number" name="cantidad" value="1" min="1" max="10">

          <div class="countBox">
            Seleccionados: <b id="selCount">0</b> / <b id="maxCount">1</b>
            <div class="muted" style="margin-top:6px; font-size:13px;">
              * Debes seleccionar exactamente la misma cantidad de asientos.
            </div>
          </div>
        </div>

        <div class="box">
          <div class="screen">PANTALLA</div>
          <div class="seats" id="seats">
            <?php foreach ($FILAS as $f): ?>
              <div class="seatRow">
                <div class="rowLabel"><?= $f ?></div>

                <?php foreach ($COLS as $c):
                  $code = $f . $c;
                  $isBusy = isset($resSet[$code]);
                ?>
                  <button
                    type="button"
                    class="seat <?= $isBusy ? 'busy' : 'free' ?>"
                    data-seat="<?= htmlspecialchars($code) ?>"
                    <?= $isBusy ? 'disabled' : '' ?>
                    title="<?= $isBusy ? 'Reservado' : 'Libre' ?>"
                  ><?= $c ?></button>
                <?php endforeach; ?>

              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="actions">
          <button class="btn" id="btnConfirmar" type="submit">Confirmar y generar ticket</button>
          <a class="backLink" href="index.php">Volver</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const cantidadInput = document.getElementById('cantidad');
  const selCountEl = document.getElementById('selCount');
  const maxCountEl = document.getElementById('maxCount');
  const asientosJson = document.getElementById('asientos_json');
  const btnConfirmar = document.getElementById('btnConfirmar');
  const seatButtons = document.querySelectorAll('.seat.free');

  let selected = [];

  function maxCount() {
    let v = parseInt(cantidadInput.value || '1', 10);
    if (isNaN(v) || v < 1) v = 1;
    if (v > 10) v = 10;
    return v;
  }

  function refreshUI() {
    selCountEl.textContent = selected.length;
    maxCountEl.textContent = maxCount();
    asientosJson.value = JSON.stringify(selected);
    btnConfirmar.disabled = (selected.length !== maxCount());
  }

  cantidadInput.addEventListener('change', () => {
    const m = maxCount();
    if (selected.length > m) {
      const toRemove = selected.slice(m);
      toRemove.forEach(code => {
        const btn = document.querySelector(`.seat.free[data-seat="${code}"]`);
        if (btn) btn.classList.remove('selected');
      });
      selected = selected.slice(0, m);
    }
    refreshUI();
  });

  seatButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const code = btn.getAttribute('data-seat');
      const m = maxCount();

      if (btn.classList.contains('selected')) {
        btn.classList.remove('selected');
        selected = selected.filter(s => s !== code);
      } else {
        if (selected.length >= m) return;
        btn.classList.add('selected');
        selected.push(code);
      }
      refreshUI();
    });
  });

  refreshUI();
</script>

</body>
</html>
