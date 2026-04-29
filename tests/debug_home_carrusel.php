<?php
/**
 * Debug específico para el carrusel en el home
 */

if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config/conexion.php";

echo "<h1>Debug - Carrusel en el Home</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; } pre { background: #333; padding: 10px; border-radius: 5px; overflow-x: auto; }</style>";

try {
    echo "<h2>1. Ejecutando la MISMA consulta que usa index.php</h2>";
    
    // Esta es la consulta EXACTA del index.php
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
            END as fecha_estreno_contenido,
            CASE 
                WHEN c.tipo = 'pelicula' THEN (
                    SELECT ROUND(AVG(cr.puntuacion), 1)
                    FROM critica cr
                    WHERE cr.id_pelicula = p.id
                )
                WHEN c.tipo = 'serie' THEN (
                    SELECT ROUND(AVG(cs.puntuacion), 1)
                    FROM critica_serie cs
                    WHERE cs.id_serie = s.id
                )
            END as media_puntuacion
        FROM carrusel_destacado c
        LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
        LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
        WHERE c.activo = 1 
        AND (c.fecha_inicio IS NULL OR c.fecha_inicio <= CURDATE())
        AND (c.fecha_fin IS NULL OR c.fecha_fin >= CURDATE())
        ORDER BY c.orden ASC, c.id DESC
        LIMIT 6
    ";
    
    echo "<pre>$sqlCarousel</pre>";
    
    $stmCarousel = $pdo->prepare($sqlCarousel);
    $stmCarousel->execute();
    $carouselPeliculas = $stmCarousel->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>2. Resultados de la consulta</h2>";
    
    if (empty($carouselPeliculas)) {
        echo "❌ <strong>LA CONSULTA NO DEVUELVE RESULTADOS</strong><br>";
        echo "Esto explica por qué no se ve nada en el home.<br><br>";
        
        // Vamos a verificar paso a paso
        echo "<h3>Verificación paso a paso:</h3>";
        
        // Paso 1: ¿Hay slides?
        $totalSlides = $pdo->query("SELECT COUNT(*) FROM carrusel_destacado")->fetchColumn();
        echo "Total slides en BD: $totalSlides<br>";
        
        // Paso 2: ¿Hay slides activos?
        $slidesActivos = $pdo->query("SELECT COUNT(*) FROM carrusel_destacado WHERE activo = 1")->fetchColumn();
        echo "Slides activos: $slidesActivos<br>";
        
        // Paso 3: ¿Hay slides sin fechas restrictivas?
        $slidesSinFechas = $pdo->query("
            SELECT COUNT(*) FROM carrusel_destacado 
            WHERE activo = 1 
            AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
            AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
        ")->fetchColumn();
        echo "Slides sin restricciones de fecha: $slidesSinFechas<br>";
        
        // Paso 4: Verificar el JOIN
        $slidesConContenido = $pdo->query("
            SELECT COUNT(*) FROM carrusel_destacado c
            LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
            LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
            WHERE c.activo = 1 
            AND (c.fecha_inicio IS NULL OR c.fecha_inicio <= CURDATE())
            AND (c.fecha_fin IS NULL OR c.fecha_fin >= CURDATE())
            AND (
                (c.tipo = 'pelicula' AND p.id IS NOT NULL) OR
                (c.tipo = 'serie' AND s.id IS NOT NULL)
            )
        ")->fetchColumn();
        echo "Slides con contenido válido: $slidesConContenido<br>";
        
        if ($slidesConContenido == 0) {
            echo "<br>🔍 <strong>PROBLEMA ENCONTRADO:</strong> Los slides no tienen contenido válido vinculado.<br>";
            
            // Mostrar detalles de los slides
            $slidesDetalle = $pdo->query("
                SELECT c.id, c.titulo, c.tipo, c.id_contenido, c.activo,
                       p.titulo as pelicula_titulo, s.titulo as serie_titulo
                FROM carrusel_destacado c
                LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
                LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
                WHERE c.activo = 1
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<br>Detalles de los slides activos:<br>";
            foreach ($slidesDetalle as $slide) {
                echo "- Slide ID {$slide['id']}: '{$slide['titulo']}' → ";
                echo "Tipo: {$slide['tipo']}, ID Contenido: {$slide['id_contenido']} → ";
                
                if ($slide['tipo'] == 'pelicula') {
                    if ($slide['pelicula_titulo']) {
                        echo "✅ Película encontrada: {$slide['pelicula_titulo']}";
                    } else {
                        echo "❌ Película NO encontrada (ID {$slide['id_contenido']} no existe)";
                    }
                } else {
                    if ($slide['serie_titulo']) {
                        echo "✅ Serie encontrada: {$slide['serie_titulo']}";
                    } else {
                        echo "❌ Serie NO encontrada (ID {$slide['id_contenido']} no existe)";
                    }
                }
                echo "<br>";
            }
        }
        
    } else {
        echo "✅ <strong>LA CONSULTA DEVUELVE " . count($carouselPeliculas) . " RESULTADOS</strong><br>";
        echo "Los datos están llegando correctamente. El problema debe estar en el HTML/CSS.<br><br>";
        
        echo "<h3>Datos que debería mostrar el carrusel:</h3>";
        foreach ($carouselPeliculas as $i => $p) {
            echo "<h4>Slide " . ($i + 1) . ":</h4>";
            echo "- Título: {$p['titulo']}<br>";
            echo "- Tipo: {$p['tipo']}<br>";
            echo "- Imagen fondo: {$p['imagen_fondo']}<br>";
            echo "- Logo: " . ($p['logo_titulo'] ?: 'Sin logo') . "<br>";
            echo "- Contenido vinculado: {$p['titulo_contenido']}<br>";
            echo "- Categoría: {$p['categoria']}<br>";
            echo "- Activo: " . ($p['activo'] ? 'SÍ' : 'NO') . "<br>";
            echo "<br>";
        }
    }
    
    echo "<h2>3. Verificar archivos de imagen</h2>";
    
    if (!empty($carouselPeliculas)) {
        foreach ($carouselPeliculas as $p) {
            $rutaImagen = "img/carrusel/{$p['imagen_fondo']}";
            if (file_exists($rutaImagen)) {
                echo "✅ Imagen existe: $rutaImagen<br>";
            } else {
                echo "❌ Imagen NO existe: $rutaImagen<br>";
            }
        }
    }
    
    echo "<h2>4. Verificar si el problema está en el HTML</h2>";
    
    if (!empty($carouselPeliculas)) {
        echo "Los datos están correctos. El problema puede estar en:<br>";
        echo "1. El CSS no se está cargando correctamente<br>";
        echo "2. Hay un error de JavaScript<br>";
        echo "3. Las rutas de las imágenes son incorrectas<br>";
        echo "4. Hay un error en el HTML del carrusel<br><br>";
        
        echo "Vamos a generar el HTML que debería aparecer:<br>";
        echo "<pre>";
        echo htmlspecialchars('<div class="carousel-item active">
    <div class="netflix-slide">
        <img src="img/carrusel/' . $carouselPeliculas[0]['imagen_fondo'] . '" class="netflix-slide-bg" alt="' . $carouselPeliculas[0]['titulo'] . '">
        <div class="netflix-slide-overlay"></div>
        <div class="netflix-slide-content">
            <div class="netflix-logo">
                <h1>' . $carouselPeliculas[0]['titulo'] . '</h1>
            </div>
        </div>
    </div>
</div>');
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Enlaces útiles</h2>";
echo "<a href='index.php'>Ver Home</a> | ";
echo "<a href='admin/carrusel_destacado.php'>Panel Admin</a>";
?>