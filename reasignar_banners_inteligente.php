<?php
/**
 * Script para reasignar banners de forma inteligente
 * Ejecutar desde: http://localhost/david/MMCINEMA/reasignar_banners_inteligente.php
 */

require_once "config/conexion.php";

echo "<h1>🎬 Reasignación Inteligente de Banners</h1>";
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

sort($bannersDisponibles);

echo "<h2>📊 Información</h2>";
echo "<p>Total de banners disponibles: " . count($bannersDisponibles) . "</p>";

// Obtener todas las series
$stmtSeries = $pdo->query("SELECT id, titulo, banner FROM serie ORDER BY id ASC");
$series = $stmtSeries->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Total de series: " . count($series) . "</p>";
echo "<hr>";

echo "<h2>📋 Reasignación de Banners</h2>";
echo "<p>Se asignarán los banners de forma secuencial a las series.</p>";
echo "<p>Puedes editar manualmente después si es necesario.</p>";
echo "<hr>";

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>ID</th>";
echo "<th>Título</th>";
echo "<th>Banner Anterior</th>";
echo "<th>Banner Nuevo</th>";
echo "<th>Estado</th>";
echo "</tr>";

$actualizadas = 0;

foreach ($series as $index => $s) {
    // Asignar banners de forma secuencial
    $bannerNuevo = $bannersDisponibles[$index % count($bannersDisponibles)];
    
    $bannerAnterior = !empty($s['banner']) ? $s['banner'] : '(sin banner)';
    
    try {
        $stmt = $pdo->prepare("UPDATE serie SET banner = ? WHERE id = ?");
        $stmt->execute([$bannerNuevo, $s['id']]);
        
        $estado = '✅ Actualizado';
        $color = '#e8f5e9';
        $actualizadas++;
    } catch (Exception $e) {
        $estado = '❌ Error';
        $color = '#ffebee';
    }
    
    echo "<tr style='background: $color;'>";
    echo "<td>" . $s['id'] . "</td>";
    echo "<td>" . htmlspecialchars($s['titulo']) . "</td>";
    echo "<td><small>" . htmlspecialchars(basename($bannerAnterior)) . "</small></td>";
    echo "<td><small>" . htmlspecialchars(basename($bannerNuevo)) . "</small></td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<p style='color: green; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
echo "<strong>✨ Reasignación completada!</strong><br>";
echo "Se reasignaron banners a " . $actualizadas . " series.";
echo "</p>";

echo "<hr>";
echo "<h2>📝 Notas Importantes</h2>";
echo "<ul>";
echo "<li>Los banners se asignaron de forma secuencial</li>";
echo "<li>Si algunos banners no corresponden, puedes editarlos manualmente en el admin</li>";
echo "<li>Para editar un banner, ve a: Admin → Series → Editar</li>";
echo "</ul>";

echo "<hr>";
echo "<p><a href='series.php' class='btn btn-primary' style='padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 5px;'>Ver todas las series</a></p>";

?>
