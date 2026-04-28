<?php
/**
 * Script para corregir banners que no existen
 * Ejecutar desde: http://localhost/david/MMCINEMA/corregir_banners_faltantes.php
 */

require_once "config/conexion.php";

echo "<h1>🔧 Corrección de Banners Faltantes</h1>";
echo "<hr>";

// Obtener todos los banners de la BD
$stmtBanners = $pdo->query("SELECT id, titulo, banner FROM serie WHERE banner IS NOT NULL AND banner != ''");
$series = $stmtBanners->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Analizando banners...</h2>";
echo "<p>Total de series con banner en BD: " . count($series) . "</p>";
echo "<hr>";

$bannersFaltantes = [];
$bannersOK = [];

foreach ($series as $s) {
    $rutaEnDisco = __DIR__ . '/' . $s['banner'];
    $existe = file_exists($rutaEnDisco);
    
    if (!$existe) {
        $bannersFaltantes[] = $s;
    } else {
        $bannersOK[] = $s;
    }
}

echo "<h2>📊 Resumen</h2>";
echo "<p>✅ Banners OK: " . count($bannersOK) . "</p>";
echo "<p>❌ Banners faltantes: " . count($bannersFaltantes) . "</p>";
echo "<hr>";

if (!empty($bannersFaltantes)) {
    echo "<h2>❌ Banners Faltantes</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #ffebee;'>";
    echo "<th>ID</th>";
    echo "<th>Título</th>";
    echo "<th>Banner en BD</th>";
    echo "</tr>";
    
    foreach ($bannersFaltantes as $s) {
        echo "<tr>";
        echo "<td>" . $s['id'] . "</td>";
        echo "<td>" . htmlspecialchars($s['titulo']) . "</td>";
        echo "<td><code>" . htmlspecialchars($s['banner']) . "</code></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<h2>🔧 Solución</h2>";
    echo "<p>Hay dos opciones:</p>";
    echo "<ol>";
    echo "<li><strong>Opción 1:</strong> Eliminar los banners faltantes de la BD (dejar en blanco)</li>";
    echo "<li><strong>Opción 2:</strong> Asignar un banner por defecto a todas las series sin banner válido</li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<h2>Ejecutando Opción 1: Limpiar banners faltantes</h2>";
    
    try {
        $stmt = $pdo->prepare("UPDATE serie SET banner = NULL WHERE banner IS NOT NULL AND banner != ''");
        
        foreach ($bannersFaltantes as $s) {
            $rutaEnDisco = __DIR__ . '/' . $s['banner'];
            if (!file_exists($rutaEnDisco)) {
                $updateStmt = $pdo->prepare("UPDATE serie SET banner = NULL WHERE id = ?");
                $updateStmt->execute([$s['id']]);
                echo "<p>✅ Serie ID " . $s['id'] . " (" . htmlspecialchars($s['titulo']) . ") - Banner eliminado</p>";
            }
        }
        
        echo "<p style='color: green; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
        echo "<strong>✨ Corrección completada!</strong>";
        echo "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

echo "<hr>";
echo "<h2>✅ Banners OK</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #e8f5e9;'>";
echo "<th>ID</th>";
echo "<th>Título</th>";
echo "<th>Banner</th>";
echo "</tr>";

foreach ($bannersOK as $s) {
    echo "<tr>";
    echo "<td>" . $s['id'] . "</td>";
    echo "<td>" . htmlspecialchars($s['titulo']) . "</td>";
    echo "<td><code>" . htmlspecialchars($s['banner']) . "</code></td>";
    echo "</tr>";
}

echo "</table>";

?>
