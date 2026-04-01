<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];

/* =========================
   Datos del usuario
========================= */
$stmU = $pdo->prepare("SELECT username, email, creado FROM usuario WHERE id = ?");
$stmU->execute([$usuario_id]);
$usuario = $stmU->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $usuario = [
        'username' => 'Usuario',
        'email' => '',
        'creado' => null
    ];
}

$inicial = strtoupper(substr($usuario['username'] ?? 'U', 0, 1));

/* =========================
   Tickets del usuario
========================= */
$sql = "
SELECT 
    t.id AS ticket_id,
    t.cantidad,
    t.precio_unitario,
    t.total,
    t.codigo,
    t.created_at,
    p.titulo,
    pr.fecha,
    pr.hora,
    pr.sala
FROM ticket t
JOIN proyeccion pr ON t.id_proyeccion = pr.id
JOIN pelicula p ON pr.id_pelicula = p.id
WHERE t.id_usuario = ?
ORDER BY t.created_at DESC
";

$stm = $pdo->prepare($sql);
$stm->execute([$usuario_id]);
$tickets = $stm->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   Críticas del usuario
========================= */
$sqlC = "
SELECT 
    c.id AS critica_id,
    c.contenido,
    c.puntuacion,
    c.creado,
    p.titulo,
    p.id AS pelicula_id
FROM critica c
LEFT JOIN pelicula p ON c.id_pelicula = p.id
WHERE c.id_usuario = ?
ORDER BY c.creado DESC
";
$stmC = $pdo->prepare($sqlC);
$stmC->execute([$usuario_id]);
$criticas = $stmC->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FAVORITAS (YA ESTRENADAS)
========================= */
$sqlFavoritas = "
SELECT 
    p.id,
    p.titulo,
    p.poster,
    p.fecha_estreno,
    p.duracion,
    p.edad,
    g.nombre AS genero,
    f.creado
FROM favorito f
JOIN pelicula p ON f.id_pelicula = p.id
LEFT JOIN genero g ON p.id_genero = g.id
WHERE f.id_usuario = ?
  AND p.fecha_estreno <= CURDATE()
ORDER BY f.creado DESC
";
$stmFav = $pdo->prepare($sqlFavoritas);
$stmFav->execute([$usuario_id]);
$favoritas = $stmFav->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   MI LISTA (PRÓXIMAMENTE)
========================= */
$sqlMiLista = "
SELECT 
    p.id,
    p.titulo,
    p.poster,
    p.fecha_estreno,
    p.duracion,
    p.edad,
    g.nombre AS genero,
    f.creado
FROM favorito f
JOIN pelicula p ON f.id_pelicula = p.id
LEFT JOIN genero g ON p.id_genero = g.id
WHERE f.id_usuario = ?
  AND p.fecha_estreno > CURDATE()
ORDER BY p.fecha_estreno ASC
";
$stmLista = $pdo->prepare($sqlMiLista);
$stmLista->execute([$usuario_id]);
$miLista = $stmLista->fetchAll(PDO::FETCH_ASSOC);

$num_criticas = count($criticas);
$media_valoracion = null;
$sum = 0;
$count_val = 0;

foreach ($criticas as $c) {
    if (!empty($c['puntuacion'])) {
        $sum += (int)$c['puntuacion'];
        $count_val++;
    }
}

if ($count_val > 0) {
    $media_valoracion = $sum / $count_val;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="perfil-container">

    <div class="perfil-header">
        <div class="perfil-user">
            <div class="perfil-avatar"><?= htmlspecialchars($inicial) ?></div>
            <div>
                <div style="font-weight:900;font-size:1.2rem;">
                    <?= htmlspecialchars($usuario['username'] ?? 'Usuario') ?>
                </div>
                <div class="perfil-meta">
                    <?= htmlspecialchars($usuario['email'] ?? '') ?>
                    <?php if (!empty($usuario['creado'])): ?>
                        · Desde <?= date('d/m/Y', strtotime($usuario['creado'])) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="perfil-meta">
            Tickets: <b><?= count($tickets) ?></b> ·
            Críticas: <b><?= (int)$num_criticas ?></b> ·
            Favoritas: <b><?= count($favoritas) ?></b> ·
            Mi lista: <b><?= count($miLista) ?></b>
            <?php if($media_valoracion !== null): ?>
                · Media: <b><?= number_format($media_valoracion, 1) ?>/5</b>
            <?php endif; ?>
        </div>
    </div>

    <div class="perfil-tabs">
        <button class="perfil-tab active" type="button" data-tab="favoritas">
            Mis favoritas
        </button>
        <button class="perfil-tab" type="button" data-tab="lista">
            Mi lista
        </button>
    </div>

    <div class="perfil-seccion active" id="favoritas">
        <?php if (empty($favoritas)): ?>
            <p class="perfil-vacio">Todavía no has añadido películas a favoritas.</p>
        <?php else: ?>
            <div class="favoritas-perfil-lista">
                <?php foreach ($favoritas as $f): ?>
                    <div class="favorita-perfil-item">
                        <div class="favorita-perfil-poster">
                            <img src="img/posters/<?= htmlspecialchars($f['poster'] ?: 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($f['titulo']) ?>">
                        </div>

                        <div class="favorita-perfil-info">
                            <h3><?= htmlspecialchars($f['titulo']) ?></h3>

                            <div class="favorita-perfil-fecha">
                                Añadida a favoritas el <?= !empty($f['creado']) ? date('d/m/Y H:i', strtotime($f['creado'])) : '' ?>
                            </div>
                        </div>

                        <div class="favorita-perfil-acciones">
                            <a href="pelicula.php?id=<?= (int)$f['id'] ?>" class="btn btn-primary btn-sm">
                                Ver película
                            </a>

                            <form action="backend/toggle_favorito.php" method="POST" style="margin-top:10px;">
                                <input type="hidden" name="pelicula_id" value="<?= (int)$f['id'] ?>">
                                <input type="hidden" name="redirect" value="perfil.php">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Quitar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="perfil-seccion" id="lista">
        <?php if (empty($miLista)): ?>
            <p class="perfil-vacio">Todavía no has añadido próximos estrenos a tu lista.</p>
        <?php else: ?>
            <div class="favoritas-perfil-lista">
                <?php foreach ($miLista as $f): ?>
                    <div class="favorita-perfil-item">
                        <div class="favorita-perfil-poster">
                            <img src="img/posters/<?= htmlspecialchars($f['poster'] ?: 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($f['titulo']) ?>">
                        </div>

                        <div class="favorita-perfil-info">
                            <h3><?= htmlspecialchars($f['titulo']) ?></h3>

                            <div class="favorita-perfil-fecha">
                                Guardada en tu lista el <?= !empty($f['creado']) ? date('d/m/Y H:i', strtotime($f['creado'])) : '' ?>
                            </div>
                        </div>

                        <div class="favorita-perfil-acciones">
                            <a href="pelicula.php?id=<?= (int)$f['id'] ?>" class="btn btn-primary btn-sm">
                                Ver película
                            </a>

                            <form action="backend/toggle_favorito.php" method="POST" style="margin-top:10px;">
                                <input type="hidden" name="pelicula_id" value="<?= (int)$f['id'] ?>">
                                <input type="hidden" name="redirect" value="perfil.php">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Quitar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <h2 class="perfil-title">Mis entradas</h2>

    <?php if (empty($tickets)): ?>
        <p class="perfil-vacio">No has comprado entradas todavía.</p>
    <?php else: ?>
        <div class="perfil-table-wrap">
            <table class="perfil-tabla">
                <thead>
                    <tr>
                        <th>Película</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Sala</th>
                        <th class="col-num">Cant.</th>
                        <th class="col-num">€/Entrada</th>
                        <th class="col-num">Total</th>
                        <th>Código</th>
                        <th>Comprado</th>
                        <th>PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="col-titulo"><?= htmlspecialchars($t['titulo']) ?></td>
                            <td><?= htmlspecialchars($t['fecha']) ?></td>
                            <td><?= htmlspecialchars($t['hora']) ?></td>
                            <td><?= htmlspecialchars($t['sala']) ?></td>
                            <td class="col-num"><?= (int)$t['cantidad'] ?></td>
                            <td class="col-num"><?= number_format((float)$t['precio_unitario'], 2) ?> €</td>
                            <td class="col-num col-total"><?= number_format((float)$t['total'], 2) ?> €</td>
                            <td class="col-codigo"><?= htmlspecialchars($t['codigo']) ?></td>
                            <td class="col-fecha"><?= htmlspecialchars($t['created_at']) ?></td>
                            <td>
                                <a class="btn-pdf" href="ticket_pdf.php?id=<?= (int)$t['ticket_id'] ?>" target="_blank">
                                    Descargar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 class="perfil-title">Mis críticas y valoraciones</h2>

    <?php if (empty($criticas)): ?>
        <p class="perfil-vacio">Todavía no has escrito ninguna crítica.</p>
    <?php else: ?>
        <div class="perfil-table-wrap">
            <table class="perfil-tabla perfil-criticas">
                <thead>
                    <tr>
                        <th>Película</th>
                        <th>Crítica</th>
                        <th>Valoración</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criticas as $c): ?>
                        <tr>
                            <td class="col-titulo">
                                <?php if (!empty($c['pelicula_id'])): ?>
                                    <a class="perfil-link" href="pelicula.php?id=<?= (int)$c['pelicula_id'] ?>">
                                        <?= htmlspecialchars($c['titulo'] ?? 'Película') ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($c['titulo'] ?? 'Película') ?>
                                <?php endif; ?>
                            </td>

                            <td class="col-contenido"><?= nl2br(htmlspecialchars($c['contenido'] ?? '')) ?></td>

                            <td class="col-num">
                                <?php if (!empty($c['puntuacion'])): ?>
                                    <span class="perfil-stars" aria-label="Valoración <?= (int)$c['puntuacion'] ?> de 5">
                                        <?php for ($i=1; $i<=5; $i++): ?>
                                            <span class="star <?= $i <= (int)$c['puntuacion'] ? 'on' : 'off' ?>">★</span>
                                        <?php endfor; ?>
                                    </span>
                                    <span class="perfil-rating">(<?= (int)$c['puntuacion'] ?>/5)</span>
                                <?php else: ?>
                                    <span class="perfil-rating muted">Sin valorar</span>
                                <?php endif; ?>
                            </td>

                            <td class="col-fecha"><?= !empty($c['creado']) ? date('d/m/Y H:i', strtotime($c['creado'])) : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php include "footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.perfil-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.perfil-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.perfil-seccion').forEach(sec => sec.classList.remove('active'));
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});
</script>
</body>
</html>