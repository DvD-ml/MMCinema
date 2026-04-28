<?php
require_once "config/conexion.php";

echo "<h1>🔍 Debug de Banners</h1>";
echo "<hr>";

// Obtener la primera serie
$stmt = $pdo->query("SELECT id, titulo, banner FROM serie LIMIT 1");
$serie = $stmt->fetch(PDO::FETCH_ASSOC);

if ($serie) {
    echo "<h2>Serie: " . htmlspecialchars($serie['titulo']) . "</h2>";
    echo "<p><strong>ID:</strong> " . $serie['id'] . "</p>";
    echo "<p><strong>Banner en BD:</strong> <code>" . htmlspecialchars($serie['banner']) . "</code></p>";
    
    $rutaEnDisco = __DIR__ . '/' . $serie['banner'];
    $existe = file_exists($rutaEnDisco);
    
    echo "<p><strong>Ruta en disco:</strong> <code>" . htmlspecialchars($rutaEnDisco) . "</code></p>";
    echo "<p><strong>¿Existe?:</strong> " . ($existe ? "✅ SÍ" : "❌ NO") . "</p>";
    
    if ($existe) {
        $tamaúo = filesize($rutaEnDisco);
        echo "<p><strong>Tamaúo:</strong> " . number_format($tamaúo / 1024, 2) . " KB</p>";
    }
    
    echo "<hr>";
    echo "<h2>Verificación de todas las series con banner</h2>";
    
    $stmtAll = $pdo->query("SELECT id, titulo, banner FROM serie WHERE banner IS NOT NULL AND banner != '' LIMIT 10");
    $series = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #333; color: white;'>";
    echo "<th>ID</th>";
    echo "<th>Título</th>";
    echo "<th>Banner en BD</th>";
    echo "<th>¿Existe?</th>";
    echo "</tr>";
    
    foreach ($series as $s) {
        $rutaEnDisco = __DIR__ . '/' . $s['banner'];
        $existe = file_exists($rutaEnDisco);
        $estado = $existe ? '✅ OK' : '❌ NO';
        $color = $existe ? '#e8f5e9' : '#ffebee';
        
        echo "<tr style='background: $color;'>";
        echo "<td>" . $s['id'] . "</td>";
        echo "<td>" . htmlspecialchars($s['titulo']) . "</td>";
        echo "<td><code>" . htmlspecialchars($s['banner']) . "</code></td>";
        echo "<td>$estado</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No hay series en la BD</p>";
}

?>
