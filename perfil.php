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
   CrÃ­ticas del usuario CON POSTER - PELÃCULAS
========================= */
$sqlCriticasPeliculas = "
SELECT 
    c.id AS critica_id,
    c.contenido,
    c.puntuacion,
    c.creado,
    p.titulo,
    p.poster,
    p.id AS pelicula_id
FROM critica c
LEFT JOIN pelicula p ON c.id_pelicula = p.id
WHERE c.id_usuario = ?
ORDER BY c.creado DESC
";
$stmCP = $pdo->prepare($sqlCriticasPeliculas);
$stmCP->execute([$usuario_id]);
$criticasPeliculas = $stmCP->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CrÃ­ticas del usuario CON POSTER - SERIES
========================= */
$criticasSeries = [];
try {
    $sqlCriticasSeries = "
    SELECT 
        cs.id AS critica_id,
        cs.contenido,
        cs.puntuacion,
        cs.creado,
        s.titulo,
        s.poster,
        s.id AS serie_id
    FROM critica_serie cs
    LEFT JOIN serie s ON cs.id_serie = s.id
    WHERE cs.id_usuario = ?
    ORDER BY cs.creado DESC
    ";
    $stmCS = $pdo->prepare($sqlCriticasSeries);
    $stmCS->execute([$usuario_id]);
    $criticasSeries = $stmCS->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tabla critica_serie no existe todavÃ­a
    $criticasSeries = [];
}

/* =========================
   FAVORITAS PELÃCULAS (YA ESTRENADAS) - MÃXIMO 5
========================= */
$sqlFavoritasPeliculas = "
SELECT 
    p.id,
    p.titulo,
    p.poster,
    p.fecha_estreno,
    p.duracion,
    p.edad,
    g.nombre AS genero,
    f.creado,
    'pelicula' AS tipo
FROM favorito f
JOIN pelicula p ON f.id_pelicula = p.id
LEFT JOIN genero g ON p.id_genero = g.id
WHERE f.id_usuario = ?
  AND p.fecha_estreno <= CURDATE()
ORDER BY f.creado DESC
LIMIT 5
";
$stmFavPel = $pdo->prepare($sqlFavoritasPeliculas);
$stmFavPel->execute([$usuario_id]);
$favoritasPeliculas = $stmFavPel->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FAVORITAS SERIES - MÃXIMO 5
========================= */
$favoritasSeries = [];
try {
    $sqlFavoritasSeries = "
    SELECT 
        s.id,
        s.titulo,
        s.poster,
        s.fecha_estreno,
        g.nombre AS genero,
        fs.creado,
        'serie' AS tipo
    FROM favorito_serie fs
    JOIN serie s ON fs.id_serie = s.id
    LEFT JOIN genero g ON s.id_genero = g.id
    WHERE fs.id_usuario = ?
    ORDER BY fs.creado DESC
    LIMIT 5
    ";
    $stmFavSer = $pdo->prepare($sqlFavoritasSeries);
    $stmFavSer->execute([$usuario_id]);
    $favoritasSeries = $stmFavSer->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Tabla favorito_serie no existe todavÃ­a
    $favoritasSeries = [];
}

/* =========================
   MI LISTA (PRÃ“XIMAMENTE)
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

$num_criticas_peliculas = count($criticasPeliculas);
$num_criticas_series = count($criticasSeries);
$num_criticas_total = $num_criticas_peliculas + $num_criticas_series;
$media_valoracion = null;
$sum = 0;
$count_val = 0;

foreach ($criticasPeliculas as $c) {
    if (!empty($c['puntuacion'])) {
        $sum += (int)$c['puntuacion'];
        $count_val++;
    }
}

foreach ($criticasSeries as $c) {
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
    <link rel="stylesheet" href="assets/css/styles.css">
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
                        Â· Desde <?= date('d/m/Y', strtotime($usuario['creado'])) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="perfil-meta">
            Tickets: <b><?= count($tickets) ?></b> Â·
            CrÃ­ticas: <b><?= (int)$num_criticas_total ?></b> Â·
            PelÃ­culas favoritas: <b><?= count($favoritasPeliculas) ?></b> Â·
            Series favoritas: <b><?= count($favoritasSeries) ?></b> Â·
            Mi lista: <b><?= count($miLista) ?></b>
            <?php if($media_valoracion !== null): ?>
                Â· Media: <b><?= number_format($media_valoracion, 1) ?>/5</b>
            <?php endif; ?>
        </div>
    </div>

    <div class="perfil-tabs">
        <button class="perfil-tab active" type="button" data-tab="peliculas">
            Mis PelÃ­culas Favoritas
        </button>
        <button class="perfil-tab" type="button" data-tab="series">
            Mis Series Favoritas
        </button>
        <button class="perfil-tab" type="button" data-tab="lista">
            Mi Lista
        </button>
    </div>

    <div class="perfil-seccion active" id="peliculas">
        <h2 class="letterboxd-section-title">Mis PelÃ­culas Favoritas</h2>
        <?php if (empty($favoritasPeliculas)): ?>
            <p class="perfil-vacio">TodavÃ­a no has aÃ±adido pelÃ­culas a favoritas.</p>
        <?php else: ?>
            <div class="letterboxd-grid">
                <?php foreach ($favoritasPeliculas as $f): ?>
                    <div class="letterboxd-item">
                        <a href="pelicula.php?id=<?= (int)$f['id'] ?>" class="letterboxd-poster-link">
                            <div class="letterboxd-poster">
<img src="assets/img/posters/<?= htmlspecialchars($f['poster'] ?: 'placeholder.jpg') ?>" 

                                     alt="<?= htmlspecialchars($f['titulo']) ?>">
                                <div class="letterboxd-overlay">
                                    <div class="letterboxd-title"><?= htmlspecialchars($f['titulo']) ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="perfil-seccion" id="series">
        <h2 class="letterboxd-section-title">Mis Series Favoritas</h2>
        <?php if (empty($favoritasSeries)): ?>
            <p class="perfil-vacio">TodavÃ­a no has aÃ±adido series a favoritas.</p>
        <?php else: ?>
            <div class="letterboxd-grid">
                <?php foreach ($favoritasSeries as $f): ?>
                    <div class="letterboxd-item">
                        <a href="serie.php?id=<?= (int)$f['id'] ?>" class="letterboxd-poster-link">
                            <div class="letterboxd-poster">
<img src="assets/img/posters/<?= htmlspecialchars($f['poster'] ?: 'placeholder.jpg') ?>" 

                                     alt="<?= htmlspecialchars($f['titulo']) ?>">
                                <div class="letterboxd-overlay">
                                    <div class="letterboxd-title"><?= htmlspecialchars($f['titulo']) ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="perfil-seccion" id="lista">
        <h2 class="letterboxd-section-title">Mi Lista</h2>
        <?php if (empty($miLista)): ?>
            <p class="perfil-vacio">TodavÃ­a no has aÃ±adido prÃ³ximos estrenos a tu lista.</p>
        <?php else: ?>
            <div class="lista-grid">
                <?php foreach ($miLista as $f): ?>
                    <div class="lista-item">
                        <a href="pelicula.php?id=<?= (int)$f['id'] ?>" class="lista-poster-link">
                            <div class="lista-poster">
<img src="assets/img/posters/<?= htmlspecialchars($f['poster'] ?: 'placeholder.jpg') ?>" 

                                     alt="<?= htmlspecialchars($f['titulo']) ?>">
                                <div class="lista-badge">PrÃ³ximamente</div>
                                <div class="lista-overlay">
                                    <div class="lista-title"><?= htmlspecialchars($f['titulo']) ?></div>
                                    <div class="lista-date">
                                        <?= !empty($f['fecha_estreno']) ? date('d/m/Y', strtotime($f['fecha_estreno'])) : '' ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <h2 class="letterboxd-section-title" style="margin-top: 60px;">Mis CrÃ­ticas</h2>

    <div class="perfil-tabs criticas-tabs">
        <button class="perfil-tab active" type="button" data-tab="criticas-peliculas">
            CrÃ­ticas de PelÃ­culas
        </button>
        <button class="perfil-tab" type="button" data-tab="criticas-series">
            CrÃ­ticas de Series
        </button>
    </div>

    <div class="perfil-seccion active" id="criticas-peliculas">
        <?php if (empty($criticasPeliculas)): ?>
            <p class="perfil-vacio">TodavÃ­a no has escrito ninguna crÃ­tica de pelÃ­culas.</p>
        <?php else: ?>
            <div class="criticas-section-wrapper">
                <?php if (count($criticasPeliculas) > 6): ?>
                    <div class="carousel-arrow carousel-arrow-left" onclick="scrollCriticasPeliculas(-1)">â€¹</div>
                <?php endif; ?>
                
                <div class="criticas-carousel-container">
                    <div class="criticas-letterboxd-grid" id="criticasPeliculasGrid">
                        <?php foreach ($criticasPeliculas as $index => $c): ?>
                            <div class="critica-letterboxd-item" onclick="openCriticaPeliculaModal(<?= $index ?>)">
                                <div class="critica-poster-link">
                                    <div class="critica-poster">
<img src="assets/img/posters/<?= htmlspecialchars($c['poster'] ?: 'placeholder.jpg') ?>" 

                                             alt="<?= htmlspecialchars($c['titulo']) ?>">
                                    </div>
                                </div>
                                <div class="critica-info">
                                    <span class="critica-titulo">
                                        <?= htmlspecialchars($c['titulo'] ?? 'PelÃ­cula') ?>
                                    </span>
                                    <div class="critica-stars">
                                        <?php if (!empty($c['puntuacion'])): ?>
                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                <span class="star-letterboxd <?= $i <= (int)$c['puntuacion'] ? 'filled' : '' ?>">â˜…</span>
                                            <?php endfor; ?>
                                        <?php else: ?>
                                            <span class="no-rating">Sin valoraciÃ³n</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (count($criticasPeliculas) > 6): ?>
                    <div class="carousel-arrow carousel-arrow-right" onclick="scrollCriticasPeliculas(1)">â€º</div>
                <?php endif; ?>
            </div>

            <!-- Modal de crÃ­tica pelÃ­cula -->
            <div class="critica-modal" id="criticaPeliculaModal" onclick="closeCriticaPeliculaModal(event)">
                <div class="critica-modal-content" onclick="event.stopPropagation()">
                    <div class="critica-modal-close" onclick="closeCriticaPeliculaModal(event)">Ã—</div>
                    <div class="critica-modal-header">
                        <div class="critica-modal-poster">
                            <img id="modalPeliculaPoster" src="" alt="">
                        </div>
                        <div class="critica-modal-info">
                            <h3 class="critica-modal-title" id="modalPeliculaTitulo"></h3>
                            <div class="critica-modal-stars" id="modalPeliculaStars"></div>
                            <div class="critica-modal-meta" id="modalPeliculaFecha"></div>
                        </div>
                    </div>
                    <div class="critica-modal-body">
                        <p class="critica-modal-text" id="modalPeliculaContenido"></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="perfil-seccion" id="criticas-series">
        <?php if (empty($criticasSeries)): ?>
            <p class="perfil-vacio">TodavÃ­a no has escrito ninguna crÃ­tica de series.</p>
        <?php else: ?>
            <div class="criticas-section-wrapper">
                <?php if (count($criticasSeries) > 6): ?>
                    <div class="carousel-arrow carousel-arrow-left" onclick="scrollCriticasSeries(-1)">â€¹</div>
                <?php endif; ?>
                
                <div class="criticas-carousel-container">
                    <div class="criticas-letterboxd-grid" id="criticasSeriesGrid">
                        <?php foreach ($criticasSeries as $index => $c): ?>
                            <div class="critica-letterboxd-item" onclick="openCriticaSerieModal(<?= $index ?>)">
                                <div class="critica-poster-link">
                                    <div class="critica-poster">
<img src="assets/img/posters/<?= htmlspecialchars($c['poster'] ?: 'placeholder.jpg') ?>" 

                                             alt="<?= htmlspecialchars($c['titulo']) ?>">
                                    </div>
                                </div>
                                <div class="critica-info">
                                    <span class="critica-titulo">
                                        <?= htmlspecialchars($c['titulo'] ?? 'Serie') ?>
                                    </span>
                                    <div class="critica-stars">
                                        <?php if (!empty($c['puntuacion'])): ?>
                                            <?php for ($i=1; $i<=5; $i++): ?>
                                                <span class="star-letterboxd <?= $i <= (int)$c['puntuacion'] ? 'filled' : '' ?>">â˜…</span>
                                            <?php endfor; ?>
                                        <?php else: ?>
                                            <span class="no-rating">Sin valoraciÃ³n</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if (count($criticasSeries) > 6): ?>
                    <div class="carousel-arrow carousel-arrow-right" onclick="scrollCriticasSeries(1)">â€º</div>
                <?php endif; ?>
            </div>

            <!-- Modal de crÃ­tica serie -->
            <div class="critica-modal" id="criticaSerieModal" onclick="closeCriticaSerieModal(event)">
                <div class="critica-modal-content" onclick="event.stopPropagation()">
                    <div class="critica-modal-close" onclick="closeCriticaSerieModal(event)">Ã—</div>
                    <div class="critica-modal-header">
                        <div class="critica-modal-poster">
                            <img id="modalSeriePoster" src="" alt="">
                        </div>
                        <div class="critica-modal-info">
                            <h3 class="critica-modal-title" id="modalSerieTitulo"></h3>
                            <div class="critica-modal-stars" id="modalSerieStars"></div>
                            <div class="critica-modal-meta" id="modalSerieFecha"></div>
                        </div>
                    </div>
                    <div class="critica-modal-body">
                        <p class="critica-modal-text" id="modalSerieContenido"></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    const criticasPeliculasData = <?= json_encode($criticasPeliculas) ?>;
    const criticasSeriesData = <?= json_encode($criticasSeries) ?>;
    let scrollPositionPeliculas = 0;
    let scrollPositionSeries = 0;

    function scrollCriticasPeliculas(direction) {
        const grid = document.getElementById('criticasPeliculasGrid');
        if (!grid || !grid.children.length) return;
        
        const itemWidth = 160; // width fijo
        const gap = 20; // gap entre items
        const itemTotalWidth = itemWidth + gap;
        const visibleItems = 6;
        const maxScroll = Math.max(0, criticasPeliculasData.length - visibleItems);
        
        scrollPositionPeliculas = Math.max(0, Math.min(maxScroll, scrollPositionPeliculas + direction));
        
        grid.style.transform = `translateX(-${scrollPositionPeliculas * itemTotalWidth}px)`;
        
        const leftArrow = document.querySelector('#criticas-peliculas .carousel-arrow-left');
        const rightArrow = document.querySelector('#criticas-peliculas .carousel-arrow-right');
        
        if (leftArrow) leftArrow.classList.toggle('disabled', scrollPositionPeliculas === 0);
        if (rightArrow) rightArrow.classList.toggle('disabled', scrollPositionPeliculas >= maxScroll);
    }

    function scrollCriticasSeries(direction) {
        const grid = document.getElementById('criticasSeriesGrid');
        if (!grid || !grid.children.length) return;
        
        const itemWidth = 160; // width fijo
        const gap = 20; // gap entre items
        const itemTotalWidth = itemWidth + gap;
        const visibleItems = 6;
        const maxScroll = Math.max(0, criticasSeriesData.length - visibleItems);
        
        scrollPositionSeries = Math.max(0, Math.min(maxScroll, scrollPositionSeries + direction));
        
        grid.style.transform = `translateX(-${scrollPositionSeries * itemTotalWidth}px)`;
        
        const leftArrow = document.querySelector('#criticas-series .carousel-arrow-left');
        const rightArrow = document.querySelector('#criticas-series .carousel-arrow-right');
        
        if (leftArrow) leftArrow.classList.toggle('disabled', scrollPositionSeries === 0);
        if (rightArrow) rightArrow.classList.toggle('disabled', scrollPositionSeries >= maxScroll);
    }

    function openCriticaPeliculaModal(index) {
        const critica = criticasPeliculasData[index];
        const modal = document.getElementById('criticaPeliculaModal');
        
document.getElementById('modalPeliculaPoster').src = 'assets/img/posters/' + (critica.poster || 'placeholder.jpg');

        document.getElementById('modalPeliculaTitulo').textContent = critica.titulo || 'PelÃ­cula';
        document.getElementById('modalPeliculaContenido').textContent = critica.contenido || 'Sin crÃ­tica escrita.';
        document.getElementById('modalPeliculaFecha').textContent = 'Publicado el ' + new Date(critica.creado).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const starsContainer = document.getElementById('modalPeliculaStars');
        starsContainer.innerHTML = '';
        if (critica.puntuacion) {
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('span');
                star.className = 'star-letterboxd' + (i <= critica.puntuacion ? ' filled' : '');
                star.textContent = 'â˜…';
                starsContainer.appendChild(star);
            }
        } else {
            starsContainer.innerHTML = '<span class="no-rating">Sin valoraciÃ³n</span>';
        }
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCriticaPeliculaModal(event) {
        const modal = document.getElementById('criticaPeliculaModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function openCriticaSerieModal(index) {
        const critica = criticasSeriesData[index];
        const modal = document.getElementById('criticaSerieModal');
        
document.getElementById('modalSeriePoster').src = critica.poster ? 'assets/img/posters/' + critica.poster : 'assets/img/posters/placeholder.jpg';

        document.getElementById('modalSerieTitulo').textContent = critica.titulo || 'Serie';
        document.getElementById('modalSerieContenido').textContent = critica.contenido || 'Sin crÃ­tica escrita.';
        document.getElementById('modalSerieFecha').textContent = 'Publicado el ' + new Date(critica.creado).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const starsContainer = document.getElementById('modalSerieStars');
        starsContainer.innerHTML = '';
        if (critica.puntuacion) {
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('span');
                star.className = 'star-letterboxd' + (i <= critica.puntuacion ? ' filled' : '');
                star.textContent = 'â˜…';
                starsContainer.appendChild(star);
            }
        } else {
            starsContainer.innerHTML = '<span class="no-rating">Sin valoraciÃ³n</span>';
        }
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCriticaSerieModal(event) {
        const modal = document.getElementById('criticaSerieModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (criticasPeliculasData.length > 6) {
            const leftArrow = document.querySelector('#criticas-peliculas .carousel-arrow-left');
            if (leftArrow) leftArrow.classList.add('disabled');
        }
        if (criticasSeriesData.length > 6) {
            const leftArrow = document.querySelector('#criticas-series .carousel-arrow-left');
            if (leftArrow) leftArrow.classList.add('disabled');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCriticaPeliculaModal(e);
            closeCriticaSerieModal(e);
        }
    });
    </script>

    <h2 class="letterboxd-section-title" style="margin-top: 60px;">Mis Entradas</h2>

    <?php if (empty($tickets)): ?>
        <p class="perfil-vacio">No has comprado entradas todavÃ­a.</p>
    <?php else: ?>
        <div class="perfil-table-wrap perfil-table-extended">
            <table class="perfil-tabla">
                <thead>
                    <tr>
                        <th>PelÃ­cula</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Sala</th>
                        <th class="col-num">Cant.</th>
                        <th class="col-num">â‚¬/Entrada</th>
                        <th class="col-num">Total</th>
                        <th>CÃ³digo</th>
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
                            <td class="col-num"><?= number_format((float)$t['precio_unitario'], 2) ?> â‚¬</td>
                            <td class="col-num col-total"><?= number_format((float)$t['total'], 2) ?> â‚¬</td>
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

</div>

<?php include "footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.perfil-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        // Get the parent tabs container
        const tabsContainer = btn.closest('.perfil-tabs');
        
        // Remove active from all tabs in this container
        tabsContainer.querySelectorAll('.perfil-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Get the tab target
        const targetId = btn.dataset.tab;
        
        // Find all sections that are siblings or nearby
        const allSections = document.querySelectorAll('.perfil-seccion');
        allSections.forEach(sec => {
            if (sec.id === targetId) {
                sec.classList.add('active');
            } else if (sec.id && (
                sec.id.startsWith('peliculas') || 
                sec.id.startsWith('series') || 
                sec.id.startsWith('lista') ||
                sec.id.startsWith('criticas-')
            )) {
                // Only hide sections from the same group
                const isFavoritasGroup = ['peliculas', 'series', 'lista'].includes(sec.id);
                const isCriticasGroup = sec.id.startsWith('criticas-');
                const targetIsFavoritasGroup = ['peliculas', 'series', 'lista'].includes(targetId);
                const targetIsCriticasGroup = targetId.startsWith('criticas-');
                
                if ((isFavoritasGroup && targetIsFavoritasGroup) || (isCriticasGroup && targetIsCriticasGroup)) {
                    sec.classList.remove('active');
                }
            }
        });
    });
});
</script>
</body>
</html>


