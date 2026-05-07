<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../config/conexion.php";

// Login obligatorio
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

// Proyeccion
$proyeccion_id = isset($_GET['proyeccion_id']) ? (int)$_GET['proyeccion_id'] : 0;
if ($proyeccion_id <= 0) {
  header("Location: index.php");
  exit();
}

// Proyeccion y pelicula
$stm = $pdo->prepare("
  SELECT pr.id AS proyeccion_id, pr.fecha, pr.hora, pr.sala,
         p.id AS pelicula_id, p.titulo, p.poster
  FROM proyeccion pr
  JOIN pelicula p ON p.id = pr.id_pelicula
  WHERE pr.id = ?
");
$stm->execute([$proyeccion_id]);
$info = $stm->fetch(PDO::FETCH_ASSOC);
if (!$info) die("Proyeccion no valida.");

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
$posterWeb  = "../assets/img/posters/" . $posterFile; // ruta web
$posterAbs  = __DIR__ . "/../assets/img/posters/" . $posterFile;      // ruta real
$posterOk   = ($posterFile !== '' && file_exists($posterAbs));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reservar entradas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <style>
    :root {
      --bg: #050608;
      --panel: #12151d;
      --line: rgba(255,255,255,.1);
      --muted: #a7b0bd;
      --text: #f8fafc;
      --accent: #ff7a18;
      --green: #22c55e;
      --busy: #64748b;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: Arial, sans-serif;
      background: #0b1220;
      color: var(--text);
    }
    .wrap { max-width: 1100px; margin: 0 auto; padding: 20px; }
    .reserve-top { display: block; margin: 10px 0 18px; text-align: center; }
    .reserve-kicker { display: none; }
    h1 { margin: 0; font-size: 2rem; line-height: 1.1; letter-spacing: normal; }
    .layout { display: grid; grid-template-columns: 340px 1fr; gap: 18px; }
    @media (max-width: 980px) { .layout { grid-template-columns: 1fr; } }
    .card { background: #111827; border: 0; border-radius: 18px; padding: 18px; box-shadow: 0 12px 30px rgba(0,0,0,.25); }
    .movie-card { position: sticky; top: 16px; align-self: start; }
    .muted { color: var(--muted); }
    .poster { border-radius: 18px; overflow: hidden; background: #0f172a; }
    .poster img { width: 100%; display: block; aspect-ratio: 2 / 3; object-fit: cover; }
    .poster .empty { padding: 18px; }
    .movie-info h2 { margin: 14px 0 6px; }
    .badgeRow { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
    .badge { display: inline-flex; gap: 6px; align-items: center; padding: 8px 10px; border-radius: 999px; background: rgba(255,255,255,.06); border: 1px solid var(--line); color: #dbe3ee; font-size: 13px; white-space: nowrap; }
    .badge b { color: #fff; font-weight: 800; }
    .price { font-size: 24px; font-weight: bold; margin-top: 14px; }
    .box { margin-top: 14px; background: #0f172a; border: 1px solid #1f2937; border-radius: 16px; padding: 14px; }
    label { display: block; margin-top: 10px; margin-bottom: 6px; font-weight: bold; }
    input[type="number"] { width: 120px; min-height: 44px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,.14); background: #0b1220; color: #f8fafc; font-weight: 700; }
    .actions { display: flex; gap: 10px; align-items: center; margin-top: 14px; flex-wrap: wrap; }
    .btn { min-height: 44px; border: 0; padding: 12px 16px; border-radius: 12px; background: #f59e0b; color: #111827; font-weight: 900; cursor: pointer; }
    .btn:disabled { opacity: .6; cursor: not-allowed; }
    .backLink { min-height: 44px; color: #e5e7eb; text-decoration: none; padding: 12px 16px; border-radius: 12px; background: #1f2937; border: 1px solid #334155; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; }
    .backLink:hover { filter: brightness(1.1); }
    .screen { text-align: center; padding: 10px; border-radius: 999px; background: linear-gradient(90deg, rgba(255,122,24,.08), rgba(255,122,24,.34), rgba(255,122,24,.08)); border: 1px solid rgba(255,122,24,.45); margin-bottom: 14px; color: #fde68a; font-weight: bold; letter-spacing: .6px; }
    .legend { display: flex; gap: 10px; flex-wrap: wrap; margin: 10px 0; }
    .pill { display: flex; align-items: center; gap: 8px; padding: 8px 10px; border-radius: 999px; background: #0b1220; border: 1px solid var(--line); color: #cbd5e1; font-size: 13px; }
    .dot { width: 12px; height: 12px; border-radius: 4px; display: inline-block; }
    .dot.free { background: var(--green); }
    .dot.sel { background: var(--accent); }
    .dot.busy { background: var(--busy); }
    .seat-scroll { overflow-x: auto; overflow-y: hidden; padding: 2px 2px 8px; -webkit-overflow-scrolling: touch; scrollbar-width: thin; scrollbar-color: rgba(255,122,24,.55) transparent; }
    .seat-scroll::-webkit-scrollbar { height: 6px; }
    .seat-scroll::-webkit-scrollbar-thumb { background: rgba(255,122,24,.55); border-radius: 999px; }
    .seats { display: grid; gap: 8px; justify-content: center; min-width: max-content; }
    .seatRow { display: flex; gap: 8px; align-items: center; }
    .rowLabel { width: 22px; min-width: 22px; text-align: center; color: #94a3b8; font-weight: bold; }
    .seat { width: 36px; height: 36px; border-radius: 11px; border: 1px solid rgba(255,255,255,.13); background: #101722; color: #e5edf7; cursor: pointer; font-size: 12px; font-weight: 800; transition: transform .14s ease, background .14s ease, border-color .14s ease; }
    .seat.free { background: rgba(34,197,94,.18); border-color: rgba(34,197,94,.55); color: #dcfce7; }
    .seat.free:hover { background: rgba(34,197,94,.28); border-color: rgba(34,197,94,.82); }
    .seat.free:active { transform: scale(.94); }
    .seat.busy { background: #151922; color: #64748b; cursor: not-allowed; opacity: .72; }
    .seat.selected { background: var(--accent); border-color: var(--accent); color: #111827; font-weight: bold; }
    .countBox { margin-top: 8px; padding: 10px; background: #0b1220; border: 1px solid var(--line); border-radius: 14px; }
    @media (max-width: 768px) {
      body {
        background:
          radial-gradient(circle at 20% 0%, rgba(255,122,24,.14), transparent 28%),
          linear-gradient(180deg, #07090d 0%, var(--bg) 42%, #030405 100%);
      }
      .wrap { padding: 16px 12px 22px; }
      .reserve-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 14px; text-align: left; }
      .reserve-kicker { display: block; margin: 0 0 7px; color: var(--accent); font-size: .74rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
      h1 { font-size: clamp(1.7rem, 9vw, 2.55rem); line-height: 1; letter-spacing: -.05em; }
      .reserve-top .backLink { display: none; }
      .layout { gap: 12px; }
      .card { background: linear-gradient(180deg, rgba(23,27,37,.96), rgba(13,16,23,.96)); border: 1px solid var(--line); padding: 12px; border-radius: 18px; box-shadow: none; }
      .movie-card { position: relative; top: auto; display: grid; grid-template-columns: 92px 1fr; gap: 12px; align-items: start; }
      .poster { border-radius: 12px; }
      .poster img { height: 138px; aspect-ratio: auto; }
      .movie-info h2 { margin: 2px 0 5px; font-size: 1.02rem; line-height: 1.08; }
      .movie-info .muted { font-size: .85rem; line-height: 1.35; }
      .badgeRow { gap: 6px; margin-top: 8px; }
      .badge { padding: 6px 8px; font-size: .74rem; }
      .price { margin-top: 9px; font-size: 1.08rem; }
      .legend { margin: 2px 0 8px; gap: 7px; }
      .pill { padding: 7px 9px; font-size: .76rem; }
      .box { margin-top: 10px; padding: 11px; border-radius: 16px; }
      .box:first-of-type { display: grid; grid-template-columns: 92px 1fr; gap: 10px; align-items: end; }
      label { margin-top: 0; font-size: .85rem; }
      input[type="number"] { width: 100%; }
      .countBox { margin-top: 0; font-size: .88rem; }
      .screen { padding: 8px; margin-bottom: 11px; font-size: .78rem; }
      .seats { gap: 7px; justify-content: start; padding-right: 4px; }
      .seatRow { gap: 7px; }
      .rowLabel { width: 18px; min-width: 18px; font-size: .8rem; }
      .seat { width: 34px; height: 34px; border-radius: 10px; }
      .actions { position: sticky; bottom: 0; z-index: 5; margin: 12px -12px -12px; padding: 10px 12px calc(10px + env(safe-area-inset-bottom)); background: linear-gradient(180deg, rgba(5,6,8,.55), rgba(5,6,8,.98)); border-top: 1px solid rgba(255,255,255,.08); }
      .actions .btn, .actions .backLink { flex: 1 1 100%; width: 100%; }
      .actions .btn { background: #fff; color: #050608; border-radius: 999px; }
      .actions .backLink { border-radius: 999px; background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.13); }
    }
    @media (max-width: 420px) {
      .movie-card { grid-template-columns: 82px 1fr; }
      .poster img { height: 124px; }
      .box:first-of-type { grid-template-columns: 1fr; }
      .countBox { margin-top: 2px; }
      .seat { width: 32px; height: 32px; font-size: 11px; }
      .seatRow { gap: 6px; }
    }
  </style>
</head>
<body>

<div class="wrap">
  <div class="reserve-top">
    <div>
      <p class="reserve-kicker">Reserva de sala</p>
      <h1>Elige tus asientos</h1>
    </div>
    <a class="backLink" href="pelicula.php?id=<?= (int)$info['pelicula_id'] ?>">Volver</a>
  </div>

  <div class="layout">
    <div class="card movie-card">
      <div class="poster">
        <?php if ($posterOk): ?>
          <img src="<?= htmlspecialchars($posterWeb) ?>" alt="Poster de <?= htmlspecialchars($info['titulo']) ?>">
        <?php else: ?>
          <div class="empty muted">Sin p&oacute;ster disponible</div>
        <?php endif; ?>
      </div>

      <div class="movie-info">
        <h2><?= htmlspecialchars($info['titulo']) ?></h2>
        <div class="muted">Elige la cantidad y selecciona tus asientos.</div>

        <div class="badgeRow">
          <div class="badge"><b>Fecha</b> <?= htmlspecialchars($info['fecha']) ?></div>
          <div class="badge"><b>Hora</b> <?= htmlspecialchars($info['hora']) ?></div>
          <div class="badge"><b>Sala</b> <?= htmlspecialchars($info['sala']) ?></div>
        </div>

        <div class="price"><?= number_format($precio_unitario, 2) ?> &euro; <span class="muted" style="font-size:14px;">/ entrada</span></div>
      </div>
    </div>

    <div class="card">
      <div class="legend">
        <div class="pill"><span class="dot free"></span> Libre</div>
        <div class="pill"><span class="dot sel"></span> Seleccionado</div>
        <div class="pill"><span class="dot busy"></span> Reservado</div>
      </div>

      <form id="formReserva" action="../backend/crear_ticket.php" method="POST">
        <input type="hidden" name="proyeccion_id" value="<?= (int)$info['proyeccion_id'] ?>">
        <input type="hidden" name="asientos_json" id="asientos_json" value="[]">
        <?php require_once __DIR__ . "/../helpers/CSRF.php"; echo CSRF::campoFormulario(); ?>

        <div class="box">
          <div>
            <label for="cantidad">Cantidad</label>
            <input id="cantidad" type="number" name="cantidad" value="1" min="1" max="10">
          </div>

          <div class="countBox">
            Seleccionados: <b id="selCount">0</b> / <b id="maxCount">1</b>
            <div class="muted" style="margin-top:6px; font-size:13px;">
              Debes seleccionar exactamente la misma cantidad de asientos.
            </div>
          </div>
        </div>

        <div class="box">
          <div class="screen">PANTALLA</div>
          <div class="seat-scroll" aria-label="Mapa de asientos">
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
        </div>

        <div class="actions">
          <button class="btn" id="btnConfirmar" type="submit">Confirmar y generar ticket</button>
          <a class="backLink" href="pelicula.php?id=<?= (int)$info['pelicula_id'] ?>">Volver</a>
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
