<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

$usuario_id = isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

/* =========================
   INFO DE LA PELÍCULA
========================= */
$stm = $pdo->prepare("
    SELECT p.*, g.nombre AS genero
    FROM pelicula p
    LEFT JOIN genero g ON p.id_genero = g.id
    WHERE p.id = ?
");
$stm->execute([$id]);
$pelicula = $stm->fetch(PDO::FETCH_ASSOC);

if (!$pelicula) {
    header("Location: index.php");
    exit();
}

/* =========================
   MEDIA DE VALORACIONES
========================= */
$stmM = $pdo->prepare("
    SELECT ROUND(AVG(puntuacion), 1) AS media
    FROM critica
    WHERE id_pelicula = ?
");
$stmM->execute([$id]);
$media = $stmM->fetchColumn();

/* =========================
   PROYECCIONES
========================= */
$stm2 = $pdo->prepare("
    SELECT *
    FROM proyeccion
    WHERE id_pelicula = ?
      AND fecha >= CURDATE()
    ORDER BY fecha, hora
");
$stm2->execute([$id]);
$proyecciones = $stm2->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CRÍTICAS
========================= */
$stm3 = $pdo->prepare("
    SELECT c.*, u.username
    FROM critica c
    LEFT JOIN usuario u ON c.id_usuario = u.id
    WHERE c.id_pelicula = ?
    ORDER BY c.creado DESC
");
$stm3->execute([$id]);
$criticas = $stm3->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FAVORITA / MI LISTA
========================= */
$esFavorita = false;

if ($usuario_id > 0) {
    $stmF = $pdo->prepare("
        SELECT id
        FROM favorito
        WHERE id_usuario = ? AND id_pelicula = ?
        LIMIT 1
    ");
    $stmF->execute([$usuario_id, $id]);
    $esFavorita = (bool)$stmF->fetch();
}

/* =========================
   ES PRÓXIMAMENTE O CARTELERA
========================= */
$esProximamente = false;
if (!empty($pelicula['fecha_estreno']) && strtotime($pelicula['fecha_estreno']) > strtotime(date('Y-m-d'))) {
    $esProximamente = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>MMCinema | <?= htmlspecialchars($pelicula['titulo']) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<?php include "navbar.php"; ?>

<main class="container my-5">

    <div class="row detalle-pelicula mb-5 align-items-start">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="detalle-poster-netflix">
                <img
                    src="assets/img/posters/<?= htmlspecialchars($pelicula['poster'] ?: 'placeholder.jpg') ?>"
                    class="img-fluid rounded shadow"
                    alt="<?= htmlspecialchars($pelicula['titulo']) ?>"
                >
            </div>
        </div>

        <div class="col-md-8">
            <div class="detalle-top-info">

                <h1 class="detalle-titulo netflix-title">
                    <?= htmlspecialchars($pelicula['titulo']) ?>
                </h1>

                <div class="detalle-meta-netflix">
                    <span><?= htmlspecialchars($pelicula['genero'] ?: 'Sin género') ?></span>
                    <span><?= htmlspecialchars($pelicula['edad'] ?: 'TP') ?></span>
                    <span><?= (int)($pelicula['duracion'] ?? 0) ?> min</span>
                    <span>Estreno: <?= !empty($pelicula['fecha_estreno']) ? date('d/m/Y', strtotime($pelicula['fecha_estreno'])) : '-' ?></span>
                </div>

                <?php if ($media !== null): ?>
                    <?php
                    $m = round(((float)$media) * 2) / 2;
                    $full = floor($m);
                    $half = ($m - $full) == 0.5;
                    ?>
                    <div class="mb-3">
                        <span class="stars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $full): ?>
                                    <span class="star on">★</span>
                                <?php elseif($i == $full + 1 && $half): ?>
                                    <span class="star half">★</span>
                                <?php else: ?>
                                    <span class="star off">★</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </span>
                        <small class="text-muted ms-1">(<?= number_format((float)$media, 1) ?>/5)</small>
                    </div>
                <?php endif; ?>

                <p class="detalle-sinopsis-netflix">
                    <?= nl2br(htmlspecialchars($pelicula['sinopsis'] ?? '')) ?>
                </p>

                <div class="detalle-acciones-netflix">
                    <?php if (!$esProximamente && !empty($proyecciones)): ?>
                        <a href="#horarios" class="btn btn-primary btn-lg detalle-btn-main">
                            Reservar entradas
                        </a>
                    <?php elseif ($esProximamente): ?>
                        <a href="proximamente.php" class="btn btn-primary btn-lg detalle-btn-main">
                            Ver próximos estrenos
                        </a>
                    <?php endif; ?>

                    <?php if ($usuario_id > 0): ?>
                        <form action="backend/toggle_favorito.php" method="POST" class="detalle-mi-lista-form">
                            <input type="hidden" name="pelicula_id" value="<?= (int)$pelicula['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

                            <button type="submit" class="detalle-mi-lista-btn <?= $esFavorita ? 'activo' : '' ?>">
                                <span class="detalle-mi-lista-icon">
                                    <?= $esFavorita ? '✓' : '+' ?>
                                </span>
                                <span>
                                    <?= $esProximamente
                                        ? ($esFavorita ? 'En mi lista' : 'Añadir a mi lista')
                                        : ($esFavorita ? 'En favoritas' : 'Añadir a favoritas') ?>
                                </span>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="detalle-mi-lista-btn ghost">
                            <span class="detalle-mi-lista-icon">+</span>
                            <span><?= $esProximamente ? 'Mi lista' : 'Favoritas' ?></span>
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <?php if (!empty($pelicula['trailer'])): ?>
                    <h4 class="mb-3">Trailer</h4>
                    <div class="ratio ratio-16x9">
                        <iframe
                            src="<?= htmlspecialchars($pelicula['trailer']) ?>"
                            title="Trailer de <?= htmlspecialchars($pelicula['titulo']) ?>"
                            frameborder="0"
                            allowfullscreen>
                        </iframe>
                    </div>
            <?php endif; ?>
        </div>
    </div>

    <h2 id="horarios" class="mb-3">Horarios Disponibles</h2>

    <?php if ($esProximamente): ?>
        <div class="alert alert-info">
            Esta película todavía no se ha estrenado.
        </div>
    <?php elseif (empty($proyecciones)): ?>
        <div class="alert alert-warning">
            No hay proyecciones programadas por ahora.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($proyecciones as $p): ?>
                <div class="col-md-3 mb-3">
                    <div class="card h-100 p-3 bg-secondary">
                        <p class="mb-1">Fecha: <?= date('d/m/Y', strtotime($p['fecha'])) ?></p>
                        <p class="mb-3">Hora: <?= date('H:i', strtotime($p['hora'])) ?></p>

                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <a href="reservar_entradas.php?proyeccion_id=<?= (int)$p['id'] ?>" class="btn btn-primary btn-sm">
                                Reservar
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">Inicia sesión para reservar.</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <hr class="my-5">

    <h2 class="mb-3">Críticas</h2>

    <?php if (empty($criticas)): ?>
        <p class="text-muted">Sé el primero en dejar una crítica para esta película.</p>
    <?php else: ?>
        <?php foreach($criticas as $c): ?>
            <div class="critica mb-3">
                <strong><?= htmlspecialchars($c['username'] ?: 'Anónimo') ?></strong>
                <small> — <?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?></small>

                <p><?= nl2br(htmlspecialchars($c['contenido'] ?? $c['texto'] ?? '')) ?></p>

                <?php if(!empty($c['puntuacion'])): ?>
                    <div class="mb-1">
                        <span class="stars" aria-label="Valoración <?= (int)$c['puntuacion'] ?> de 5">
                            <?php for($i=1;$i<=5;$i++): ?>
                                <span class="star <?= ($i <= (int)$c['puntuacion']) ? 'on' : 'off' ?>">★</span>
                            <?php endfor; ?>
                        </span>
                        <small class="text-muted">(<?= (int)$c['puntuacion'] ?>/5)</small>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <?php if(isset($_SESSION['usuario_id'])): ?>
        <h4>Escribe tu crítica</h4>
        <form action="backend/enviar_critica.php" method="POST">
            <input type="hidden" name="pelicula_id" value="<?= (int)$id ?>">

            <div class="mb-2">
                <label class="form-label">Tu opinión</label>
                <textarea name="contenido" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-2">
                <label class="form-label">Valoración (1-5 ⭐)</label>
                <select name="puntuacion" class="form-control">
                    <option value="">Sin valorar</option>
                    <?php for($i=1;$i<=5;$i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                    <?php endfor; ?>
                </select>
            </div>

            <button class="btn btn-primary">Enviar crítica</button>
        </form>
    <?php else: ?>
        <p class="text-muted">Debes <a href="login.php">iniciar sesión</a> para escribir una crítica o reservar.</p>
    <?php endif; ?>

</main>

<?php include "footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- <?php include "includes/lenis-scripts.php"; ?> -->
</body>
</html>