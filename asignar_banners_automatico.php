<?php
/**
 * Script para asignar banners automáticamente a las series
 * Ejecutar desde: http://localhost/david/MMCINEMA/asignar_banners_automatico.php
 */

require_once "config/conexion.php";

echo "<h1>🎬 Asignación Automática de Banners</h1>";
echo "<hr>";

// Obtener todos los banners disponibles
$bannersDisponibles = [];
$bannerDir = __DIR__ . '/assets/img/series/banners/';

if (is_dir($bannerDir)) {
    $archivos = scandir($bannerDir);
    foreach ($archivos as $archivo) {
        if ($archivo !== '.' && $archivo !== '..' && is_file($bannerDir . $archivo)) {
            $bannersDisponibles[] = 'assets/img/series/banners/' . $archivo;
        }
    }
}

echo "<h2>📊 Banners Disponibles</h2>";
echo "<p>Total de banners en disco: " . count($bannersDisponibles) . "</p>";

if (empty($bannersDisponibles)) {
    echo "<p style='color: red;'>❌ No hay banners disponibles en assets/img/series/banners/</p>";
    exit;
}

echo "<hr>";

// Obtener todas las series
$stmtSeries = $pdo->query("SELECT id, titulo, banner FROM serie ORDER BY id ASC");
$series = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>📋 Series sin Banner</h2>";

$seriesSinBanner = [];
$seriesConBanner = [];

foreach ($series as $s) {
    if (empty($s['banner'])) {
        $seriesSinBanner[] = $s;
    } else {
        $seriesConBanner[] = $s;
    }
}

echo "<p>✅ Series con banner: " . count($seriesConBanner) . "</p>";
echo "<p>❌ Series sin banner: " . count($seriesSinBanner) . "</p>";

echo "<hr>";

if (!empty($seriesSinBanner)) {
    echo "<h2>🔧 Asignando banners automáticamente...</h2>";
    
    $bannerIndex = 0;
    $actualizadas = 0;
    
    foreach ($seriesSinBanner as $s) {
        // Asignar banners de forma cíclica
        $banner = $bannersDisponibles[$bannerIndex % count($bannersDisponibles)];
        
        try {
            $stmt = $pdo->prepare("UPDATE serie SET banner = ? WHERE id = ?");
            $stmt->execute([$banner, $s['id']]);
            
            echo "<p>✅ Serie ID " . $s['id'] . " (" . htmlspecialchars($s['titulo']) . ") - Banner asignado</p>";
            $actualizadas++;
            $bannerIndex++;
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error en serie ID " . $s['id'] . ": " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<p style='color: green; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
    echo "<strong>✨ Asignación completada!</strong><br>";
    echo "Se asignaron banners a " . $actualizadas . " series.";
    echo "</p>";
} else {
    echo "<p style='color: green;'>✅ Todas las series ya tienen banner asignado.</p>";
}

echo "<hr>";

// Verificación final
echo "<h2>✅ Verificación Final</h2>";

$stmtFinal = $pdo->query("SELECT COUNT(*) as total FROM serie WHERE banner IS NOT NULL AND banner != ''");
$resultado = $stmtFinal->fetch(PDO::FETCH_ASSOC);

echo "<p>Total de series con banner: " . $resultado['total'] . "</p>";

echo "<hr>";
echo "<p><a href='serie.php?id=1' class='btn btn-primary' style='padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 5px;'>Ver serie con banner</a></p>";

?>
