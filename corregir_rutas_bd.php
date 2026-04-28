<?php
/**
 * Script para corregir rutas de imágenes en la BD
 * Ejecutar desde: http://localhost/david/MMCINEMA/corregir_rutas_bd.php
 */

require_once "config/conexion.php";

echo "<h1>🔧 Corrección de Rutas en BD</h1>";
echo "<hr>";

// ============================================
// 1. CORREGIR POSTERS DE TEMPORADAS
// ============================================
echo "<h2>1️⃣ Corrigiendo posters de temporadas...</h2>";

try {
    $stmt = $pdo->prepare("
        UPDATE temporada 
        SET poster = CONCAT('assets/', poster)
        WHERE poster IS NOT NULL 
        AND poster != '' 
        AND poster NOT LIKE 'assets/%'
    ");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "<p style='color: green;'>✅ $count registros actualizados</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br>";

// ============================================
// 2. CORREGIR POSTERS DE SERIES
// ============================================
echo "<h2>2️⃣ Corrigiendo posters de series...</h2>";

try {
    $stmt = $pdo->prepare("
        UPDATE serie 
        SET poster = CONCAT('assets/', poster)
        WHERE poster IS NOT NULL 
        AND poster != '' 
        AND poster NOT LIKE 'assets/%'
    ");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "<p style='color: green;'>✅ $count registros actualizados</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br>";

// ============================================
// 3. CORREGIR BANNERS DE SERIES
// ============================================
echo "<h2>3️⃣ Corrigiendo banners de series...</h2>";

try {
    $stmt = $pdo->prepare("
        UPDATE serie 
        SET banner = CONCAT('assets/', banner)
        WHERE banner IS NOT NULL 
        AND banner != '' 
        AND banner NOT LIKE 'assets/%'
    ");
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "<p style='color: green;'>✅ $count registros actualizados</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<br>";

// ============================================
// 4. VERIFICAR RESULTADOS
// ============================================
echo "<h2>4️⃣ Verificando resultados...</h2>";

$stmtBanners = $pdo->query("SELECT COUNT(*) FROM serie WHERE banner IS NOT NULL AND banner != ''");
$totalBanners = $stmtBanners->fetchColumn();

$stmtTemporadas = $pdo->query("SELECT COUNT(*) FROM temporada WHERE poster IS NOT NULL AND poster != ''");
$totalTemporadas = $stmtTemporadas->fetchColumn();

$stmtPosters = $pdo->query("SELECT COUNT(*) FROM serie WHERE poster IS NOT NULL AND poster != ''");
$totalPosters = $stmtPosters->fetchColumn();

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>Tipo</th>";
echo "<th>Total</th>";
echo "</tr>";
echo "<tr>";
echo "<td>Banners de series</td>";
echo "<td>$totalBanners</td>";
echo "</tr>";
echo "<tr>";
echo "<td>Posters de temporadas</td>";
echo "<td>$totalTemporadas</td>";
echo "</tr>";
echo "<tr>";
echo "<td>Posters de series</td>";
echo "<td>$totalPosters</td>";
echo "</tr>";
echo "</table>";

echo "<br>";

// ============================================
// 5. RESUMEN
// ============================================
echo "<h2>✨ Resumen</h2>";
echo "<p style='padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
echo "<strong>🎉 ¡Corrección completada!</strong><br>";
echo "Todas las rutas han sido actualizadas en la BD.<br>";
echo "Ahora deberían verse correctamente en el navegador.";
echo "</p>";

echo "<br>";
echo "<p><a href='verificar_rutas.php' class='btn btn-primary' style='padding: 10px 20px; background: #1976d2; color: white; text-decoration: none; border-radius: 5px;'>Verificar rutas nuevamente</a></p>";

?>
