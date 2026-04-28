<?php
/**
 * Debug - Verificar slides del carrusel
 */

require_once "config/conexion.php";

echo "<h1>Debug - Slides del Carrusel</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #333; padding: 8px; text-align: left; } th { background: #333; }</style>";

try {
    echo "<h2>1. Slides en la Base de Datos</h2>";
    
    $sqlSlides = "
        SELECT 
            c.*,
            CASE 
                WHEN c.tipo = 'pelicula' THEN p.titulo
                WHEN c.tipo = 'serie' THEN s.titulo
            END as titulo_contenido,
            CASE 
                WHEN c.tipo = 'pelicula' THEN p.poster
                WHEN c.tipo = 'serie' THEN s.poster
            END as poster_contenido
        FROM carrusel_destacado c
        LEFT JOIN pelicula p ON c.tipo = 'pelicula' AND c.id_contenido = p.id
        LEFT JOIN serie s ON c.tipo = 'serie' AND c.id_contenido = s.id
        ORDER BY c.orden ASC, c.id DESC
    ";
    
    $stmSlides = $pdo->prepare($sqlSlides);
    $stmSlides->execute();
    $slides = $stmSlides->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($slides)) {
        echo "❌ No hay slides en la base de datos<br>";
    } else {
        echo "✅ Encontrados " . count($slides) . " slides:<br><br>";
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Título</th><th>Tipo</th><th>ID Contenido</th><th>Imagen Fondo</th><th>Logo</th><th>Activo</th><th>Orden</th><th>Contenido Vinculado</th></tr>";
        
        foreach ($slides as $slide) {
            echo "<tr>";
            echo "<td>{$slide['id']}</td>";
            echo "<td>{$slide['titulo']}</td>";
            echo "<td>{$slide['tipo']}</td>";
            echo "<td>{$slide['id_contenido']}</td>";
            echo "<td>{$slide['imagen_fondo']}</td>";
            echo "<td>" . ($slide['logo_titulo'] ?: 'Sin logo') . "</td>";
            echo "<td>" . ($slide['activo'] ? 'SÍ' : 'NO') . "</td>";
            echo "<td>{$slide['orden']}</td>";
            echo "<td>" . ($slide['titulo_contenido'] ?: 'NO ENCONTRADO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>2. Verificar Archivos de Imagen</h2>";
    
    foreach ($slides as $slide) {
        echo "<h3>Slide: {$slide['titulo']}</h3>";
        
        // Verificar imagen de fondo
        $ruta_fondo = "img/carrusel/{$slide['imagen_fondo']}";
        if (file_exists($ruta_fondo)) {
            $size = filesize($ruta_fondo);
            echo "✅ Imagen de fondo existe: $ruta_fondo (" . number_format($size/1024, 2) . " KB)<br>";
            echo "<img src='$ruta_fondo' style='max-width: 200px; max-height: 100px; border: 1px solid #333; margin: 5px 0;'><br>";
        } else {
            echo "❌ Imagen de fondo NO existe: $ruta_fondo<br>";
        }
        
        // Verificar logo si existe
        if ($slide['logo_titulo']) {
            $ruta_logo = "img/logos/{$slide['logo_titulo']}";
            if (file_exists($ruta_logo)) {
                $size = filesize($ruta_logo);
                echo "✅ Logo existe: $ruta_logo (" . number_format($size/1024, 2) . " KB)<br>";
                echo "<img src='$ruta_logo' style='max-width: 100px; max-height: 50px; border: 1px solid #333; margin: 5px 0;'><br>";
            } else {
                echo "❌ Logo NO existe: $ruta_logo<br>";
            }
        }
        
        echo "<br>";
    }
    
    echo "<h2>3. Consulta que Usa el Index</h2>";
    
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
    
    if (empty($carouselPeliculas)) {
        echo "❌ La consulta del index NO devuelve resultados<br>";
        echo "Posibles causas:<br>";
        echo "- Los slides no están activos (activo = 0)<br>";
        echo "- Hay fechas de inicio/fin que filtran los slides<br>";
        echo "- El contenido vinculado (película/serie) no existe<br>";
    } else {
        echo "✅ La consulta del index devuelve " . count($carouselPeliculas) . " slides<br>";
        
        echo "<table>";
        echo "<tr><th>Título</th><th>Tipo</th><th>Imagen</th><th>Contenido Vinculado</th><th>Activo</th></tr>";
        
        foreach ($carouselPeliculas as $slide) {
            echo "<tr>";
            echo "<td>{$slide['titulo']}</td>";
            echo "<td>{$slide['tipo']}</td>";
            echo "<td>{$slide['imagen_fondo']}</td>";
            echo "<td>" . ($slide['titulo_contenido'] ?: 'NO ENCONTRADO') . "</td>";
            echo "<td>" . ($slide['activo'] ? 'SÍ' : 'NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>4. Verificar Contenido Vinculado</h2>";
    
    // Verificar películas
    $peliculas = $pdo->query("SELECT id, titulo FROM pelicula ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo "Películas disponibles: " . count($peliculas) . "<br>";
    if (!empty($peliculas)) {
        foreach (array_slice($peliculas, 0, 5) as $p) {
            echo "- ID {$p['id']}: {$p['titulo']}<br>";
        }
        if (count($peliculas) > 5) {
            echo "- ... y " . (count($peliculas) - 5) . " más<br>";
        }
    }
    
    echo "<br>";
    
    // Verificar series
    $series = $pdo->query("SELECT id, titulo FROM serie ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    echo "Series disponibles: " . count($series) . "<br>";
    if (!empty($series)) {
        foreach (array_slice($series, 0, 5) as $s) {
            echo "- ID {$s['id']}: {$s['titulo']}<br>";
        }
        if (count($series) > 5) {
            echo "- ... y " . (count($series) - 5) . " más<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Enlaces Útiles</h2>";
echo "<a href='index.php'>Ver Home</a> | ";
echo "<a href='admin/carrusel_destacado.php'>Panel Admin</a> | ";
echo "<a href='actualizar_sesion.php'>Actualizar Sesión</a>";
?>