<?php
// Test simple de autoload
echo "<h1>Test de Autoload</h1>";

echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Ruta vendor:</strong> " . __DIR__ . '/vendor/autoload.php' . "</p>";
echo "<p><strong>¿Existe vendor/autoload.php?</strong> " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'SÍ' : 'NO') . "</p>";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p style='color: green;'><strong>✅ Autoload cargado correctamente</strong></p>";
    
    // Test de Dotenv
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        echo "<p style='color: green;'><strong>✅ Dotenv cargado correctamente</strong></p>";
        
        echo "<h2>Variables de Entorno:</h2>";
        echo "<ul>";
        echo "<li><strong>DB_HOST:</strong> " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "</li>";
        echo "<li><strong>DB_NAME:</strong> " . ($_ENV['DB_NAME'] ?? 'NO DEFINIDO') . "</li>";
        echo "<li><strong>DB_USER:</strong> " . ($_ENV['DB_USER'] ?? 'NO DEFINIDO') . "</li>";
        echo "<li><strong>BASE_URL:</strong> " . ($_ENV['BASE_URL'] ?? 'NO DEFINIDO') . "</li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Error al cargar Dotenv:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'><strong>❌ No se encuentra vendor/autoload.php</strong></p>";
}
?>
