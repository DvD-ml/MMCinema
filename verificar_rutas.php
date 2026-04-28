<?php
/**
 * Script para verificar rutas de imágenes en BD vs Sistema de Archivos
 * Ejecutar desde: http://localhost/david/MMCINEMA/verificar_rutas.php
 */

require_once "config/conexion.php";

echo "<h1>🔍 Verificación de Rutas de Imágenes</h1>";
echo "<hr>";

// ============================================
// 1. VERIFICAR BANNERS DE SERIES
// ============================================
echo "<h2>1️⃣ Banners de Series</h2>";

$stmtBanners = $pdo->query("SELECT id, titulo, banner FROM serie WHERE banner IS NOT NULL AND banner != '' LIMIT 5");
$banners = $stmtBanners->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>ID</th>";
echo "<th>Título</th>";
echo "<th>Ruta en BD</th>";
echo "<th>Existe en Disco</th>";
echo "<th>Estado</th>";
echo "</tr>";

foreach ($banners as $banner) {
    $rutaEnBD = $banner['banner'];
    $rutaEnDisco = __DIR__ . '/' . $rutaEnBD;
    $existe = file_exists($rutaEnDisco);
    
    $estado = $existe ? '✅ OK' : '❌ NO EXISTE';
    $color = $existe ? '#e8f5e9' : '#ffebee';
    
    echo "<tr style='background: $color;'>";
    echo "<td>" . (int)$banner['id'] . "</td>";
    echo "<td>" . htmlspecialchars($banner['titulo']) . "</td>";
    echo "<td><code>" . htmlspecialchars($rutaEnBD) . "</code></td>";
    echo "<td>" . ($existe ? 'Sí' : 'No') . "</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}

echo "</table>";
echo "<br>";

// ============================================
// 2. VERIFICAR POSTERS DE TEMPORADAS
// ============================================
echo "<h2>2️⃣ Posters de Temporadas</h2>";

$stmtTemporadas = $pdo->query("SELECT id, numero_temporada, poster FROM temporada WHERE poster IS NOT NULL AND poster != '' LIMIT 5");
$temporadas = $stmtTemporadas->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>ID</th>";
echo "<th>Temporada</th>";
echo "<th>Ruta en BD</th>";
echo "<th>Existe en Disco</th>";
echo "<th>Estado</th>";
echo "</tr>";

foreach ($temporadas as $temporada) {
    $rutaEnBD = $temporada['poster'];
    $rutaEnDisco = __DIR__ . '/' . $rutaEnBD;
    $existe = file_exists($rutaEnDisco);
    
    $estado = $existe ? '✅ OK' : '❌ NO EXISTE';
    $color = $existe ? '#e8f5e9' : '#ffebee';
    
    echo "<tr style='background: $color;'>";
    echo "<td>" . (int)$temporada['id'] . "</td>";
    echo "<td>T" . (int)$temporada['numero_temporada'] . "</td>";
    echo "<td><code>" . htmlspecialchars($rutaEnBD) . "</code></td>";
    echo "<td>" . ($existe ? 'Sí' : 'No') . "</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}

echo "</table>";
echo "<br>";

// ============================================
// 3. VERIFICAR POSTERS DE SERIES
// ============================================
echo "<h2>3️⃣ Posters de Series</h2>";

$stmtPosters = $pdo->query("SELECT id, titulo, poster FROM serie WHERE poster IS NOT NULL AND poster != '' LIMIT 5");
$posters = $stmtPosters->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>ID</th>";
echo "<th>Título</th>";
echo "<th>Ruta en BD</th>";
echo "<th>Existe en Disco</th>";
echo "<th>Estado</th>";
echo "</tr>";

foreach ($posters as $poster) {
    $rutaEnBD = $poster['poster'];
    $rutaEnDisco = __DIR__ . '/' . $rutaEnBD;
    $existe = file_exists($rutaEnDisco);
    
    $estado = $existe ? '✅ OK' : '❌ NO EXISTE';
    $color = $existe ? '#e8f5e9' : '#ffebee';
    
    echo "<tr style='background: $color;'>";
    echo "<td>" . (int)$poster['id'] . "</td>";
    echo "<td>" . htmlspecialchars($poster['titulo']) . "</td>";
    echo "<td><code>" . htmlspecialchars($rutaEnBD) . "</code></td>";
    echo "<td>" . ($existe ? 'Sí' : 'No') . "</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}

echo "</table>";
echo "<br>";

// ============================================
// 4. RESUMEN
// ============================================
echo "<h2>✨ Resumen</h2>";

$totalBanners = $pdo->query("SELECT COUNT(*) FROM serie WHERE banner IS NOT NULL AND banner != ''")->fetchColumn();
$totalTemporadas = $pdo->query("SELECT COUNT(*) FROM temporada WHERE poster IS NOT NULL AND poster != ''")->fetchColumn();
$totalPosters = $pdo->query("SELECT COUNT(*) FROM serie WHERE poster IS NOT NULL AND poster != ''")->fetchColumn();

echo "<ul>";
echo "<li>Total de banners en BD: <strong>$totalBanners</strong></li>";
echo "<li>Total de posters de temporadas en BD: <strong>$totalTemporadas</strong></li>";
echo "<li>Total de posters de series en BD: <strong>$totalPosters</strong></li>";
echo "</ul>";

echo "<p style='margin-top: 20px; padding: 10px; background: #e3f2fd; border-radius: 5px;'>";
echo "<strong>ℹ️ Nota:</strong> Si ves ❌ NO EXISTE, significa que la ruta en BD no coincide con los archivos en el disco.";
echo "</p>";

?>
