<?php
/**
 * Test del carrusel con CSS corregido
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

// Obtener el slide
$sqlCarousel = "
    SELECT 
        c.*,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.titulo
            WHEN c.tipo = 'serie' THEN s.titulo
        END as titulo_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.sinopsis
            WHEN c.tipo = 'serie' THEN s.sinopsis
        END as sinopsis_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.duracion
            WHEN c.tipo = 'serie' THEN NULL
        END as duracion_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.edad
            WHEN c.tipo = 'serie' THEN s.edad
        END as edad_contenido,
        CASE 
            WHEN c.tipo = 'pelicula' THEN p.fecha_estreno
            WHEN c.tipo = 'serie' THEN s.fecha_estreno
        END as fecha_estreno_contenido
    FROM carrusel_destacado c
    LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
    LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
    WHERE c.activo = 1 
    AND (c.fecha_inicio IS NULL OR c.fecha_inicio <= CURDATE())
    AND (c.fecha_fin IS NULL OR c.fecha_fin >= CURDATE())
    ORDER BY c.orden ASC, c.id DESC
    LIMIT 6
";

$stmCarousel = $pdo->prepare($sqlCarousel);
$stmCarousel->execute();
$carouselPeliculas = $stmCarousel->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Test Carrusel CORREGIDO - MMCinema</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/carrusel-fix.css">
    <style>
        body { 
            background: #141414; 
            color: white; 
            margin: 0; 
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .debug-info {
            position: fixed;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.9);
            padding: 15px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 9999;
            border: 1px solid #e50914;
        }
        .test-header {
            padding: 20px;
            text-align: center;
            background: rgba(229, 9, 20, 0.1);
            margin-bottom: 0;
        }
    </style>
</head>
<body>

<div class="debug-info">
    <strong>🔧 CARRUSEL CORREGIDO</strong><br>
    Slides: <?= count($carouselPeliculas) ?><br>
    CSS Fix: ✅<br>
    Bootstrap: ✅<br>
    Bordes rojos = debug
</div>

<div class="test-header">
    <h1>🎬 Test Carrusel Netflix - VERSIÓN CORREGIDA</h1>
    <p>Con estilos forzados y bordes de debug</p>
</div>

<!-- CARRUSEL NETFLIX -->
<section class="netflix-hero-section">
    <div id="netflixCarousel" class="carousel slide netflix-carousel" data-bs-ride="carousel" data-bs-interval="6000">

        <?php if (!empty($carouselPeliculas)): ?>
            <div class="carousel-indicators netflix-indicators">
                <?php foreach ($carouselPeliculas as $i => $pelicula): ?>
                    <button type="button"
                            data-bs-target="#netflixCarousel"
                            data-bs-slide-to="<?= $i ?>"
                            class="<?= $i === 0 ? 'active' : '' ?>"
                            aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                            aria-label="Slide <?= $i + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="carousel-inner">
            <?php if (empty($carouselPeliculas)): ?>
                <div class="carousel-item active">
                    <div class="netflix-slide">
                        <div class="netflix-slide-bg" style="background: linear-gradient(135deg, #e50914, #221f1f);"></div>
                        <div class="netflix-slide-overlay"></div>
                        <div class="netflix-slide-content">
                            <div class="netflix-logo">
                                <h1>❌ No hay slides</h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($carouselPeliculas as $i => $p): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <div class="netflix-slide">
                            <img
                                src="img/carrusel/<?= htmlspecialchars($p['imagen_fondo']) ?>"
                                class="netflix-slide-bg"
                                alt="<?= htmlspecialchars($p['titulo']) ?>"
                                onerror="console.log('Error cargando imagen: <?= $p['imagen_fondo'] ?>')"
                            >
                            <div class="netflix-slide-overlay"></div>
                            
                            <div class="netflix-slide-content">
                                <div class="netflix-logo">
                                    <?php if ($p['logo_titulo']): ?>
                                        <img src="img/logos/<?= htmlspecialchars($p['logo_titulo']) ?>" 
                                             alt="<?= htmlspecialchars($p['titulo']) ?>"
                                             class="netflix-logo-img"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <h1 style="display: none;"><?= htmlspecialchars($p['titulo']) ?></h1>
                                    <?php else: ?>
                                        <h1><?= htmlspecialchars($p['titulo']) ?></h1>
                                    <?php endif; ?>
                                </div>
                                <div class="netflix-info">
                                    <span class="netflix-badge"><?= ucfirst($p['categoria']) ?></span>
                                    <span class="netflix-year"><?= date('Y', strtotime($p['fecha_estreno_contenido'])) ?></span>
                                    <?php if ($p['duracion_contenido']): ?>
                                        <span class="netflix-duration"><?= (int)$p['duracion_contenido'] ?> min</span>
                                    <?php else: ?>
                                        <span class="netflix-duration"><?= ucfirst($p['tipo']) ?></span>
                                    <?php endif; ?>
                                    <span class="netflix-rating"><?= htmlspecialchars($p['edad_contenido'] ?: 'TP') ?></span>
                                </div>
                                <div class="netflix-actions">
                                    <?php 
                                    $link = $p['tipo'] === 'serie' ? 'serie.php?id=' . $p['id_contenido'] : 'pelicula.php?id=' . $p['id_contenido'];
                                    ?>
                                    <a href="<?= $link ?>" class="netflix-btn netflix-btn-play">
                                        <span class="netflix-play-icon">▶</span>
                                        Reproducir
                                    </a>
                                    <a href="<?= $link ?>" class="netflix-btn netflix-btn-info">
                                        <span class="netflix-info-icon">ⓘ</span>
                                        Más información
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<div style="padding: 20px; text-align: center; background: rgba(255,255,255,0.1); margin: 20px;">
    <h2>🔗 Enlaces</h2>
    <a href="index.php" style="color: #e50914; margin: 0 10px;">← Volver al Home</a>
    <a href="test_carrusel_html.php" style="color: #e50914; margin: 0 10px;">Test Original</a>
    <a href="admin/carrusel_destacado.php" style="color: #e50914; margin: 0 10px;">Panel Admin</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Debug adicional
console.log('Carrusel cargado');
console.log('Slides encontrados:', <?= count($carouselPeliculas) ?>);

// Verificar que Bootstrap funciona
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('netflixCarousel');
    if (carousel) {
        console.log('Elemento carrusel encontrado');
        const bsCarousel = new bootstrap.Carousel(carousel);
        console.log('Bootstrap Carousel inicializado');
    } else {
        console.error('Elemento carrusel NO encontrado');
    }
});
</script>
</body>
</html>