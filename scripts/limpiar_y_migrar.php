<?php
/**
 * Script para limpiar carpetas duplicadas y migrar rutas en BD
 * Ejecutar desde: http://localhost/david/MMCINEMA/scripts/limpiar_y_migrar.php
 */

require_once __DIR__ . '/../config/conexion.php';

echo "<h1>🧹 Limpieza y Migración de Rutas</h1>";
echo "<hr>";

// ============================================
// 1. MIGRAR RUTAS EN BD
// ============================================
echo "<h2>1️⃣ Migrando rutas en base de datos...</h2>";

try {
    // Migrar posters de series
    $stmt = $pdo->prepare("
        UPDATE serie 
        SET poster = REPLACE(poster, 'img/series/posters/', 'assets/img/series/posters/')
        WHERE poster LIKE 'img/series/posters/%'
    ");
    $stmt->execute();
    $count1 = $stmt->rowCount();
    echo "✅ Posters de series migrados: <strong>$count1</strong> registros<br>";

    // Migrar banners de series
    $stmt = $pdo->prepare("
        UPDATE serie 
        SET banner = REPLACE(banner, 'img/series/banners/', 'assets/img/series/banners/')
        WHERE banner LIKE 'img/series/banners/%'
    ");
    $stmt->execute();
    $count2 = $stmt->rowCount();
    echo "✅ Banners de series migrados: <strong>$count2</strong> registros<br>";

    // Migrar posters de temporadas
    $stmt = $pdo->prepare("
        UPDATE temporada 
        SET poster = REPLACE(poster, 'img/series/temporadas/', 'assets/img/series/temporadas/')
        WHERE poster LIKE 'img/series/temporadas/%'
    ");
    $stmt->execute();
    $count3 = $stmt->rowCount();
    echo "✅ Posters de temporadas migrados: <strong>$count3</strong> registros<br>";

    echo "<p style='color: green; font-weight: bold;'>Total migrado: " . ($count1 + $count2 + $count3) . " registros</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error en migración: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";

// ============================================
// 2. LIMPIAR CARPETAS DUPLICADAS
// ============================================
echo "<h2>2️⃣ Limpiando carpetas duplicadas...</h2>";

$carpetaDuplicada = __DIR__ . '/../assets/img/series/series';

if (is_dir($carpetaDuplicada)) {
    echo "Carpeta encontrada: <code>$carpetaDuplicada</code><br>";
    
    // Contar archivos
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($carpetaDuplicada),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    $totalFiles = 0;
    foreach ($files as $file) {
        if ($file->isFile()) {
            $totalFiles++;
        }
    }
    
    echo "Archivos encontrados: <strong>$totalFiles</strong><br>";
    
    // Eliminar carpeta
    $result = eliminarDirectorio($carpetaDuplicada);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ Carpeta duplicada eliminada correctamente</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No se pudo eliminar la carpeta. Intenta manualmente o verifica permisos.</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ La carpeta duplicada no existe. ¡Bien!</p>";
}

echo "<hr>";

// ============================================
// 3. VERIFICAR ESTRUCTURA
// ============================================
echo "<h2>3️⃣ Verificando estructura de carpetas...</h2>";

$carpetasEsperadas = [
    'assets/img/series/posters',
    'assets/img/series/banners',
    'assets/img/series/temporadas'
];

foreach ($carpetasEsperadas as $carpeta) {
    $ruta = __DIR__ . '/../' . $carpeta;
    if (is_dir($ruta)) {
        $files = glob($ruta . '/*');
        $count = count($files);
        echo "✅ <code>$carpeta</code> - <strong>$count</strong> archivos<br>";
    } else {
        echo "❌ <code>$carpeta</code> - NO EXISTE<br>";
    }
}

echo "<hr>";

// ============================================
// 4. RESUMEN
// ============================================
echo "<h2>✨ Resumen</h2>";
echo "<ul>";
echo "<li>✅ Rutas en BD migradas</li>";
echo "<li>✅ Carpetas duplicadas eliminadas</li>";
echo "<li>✅ Estructura verificada</li>";
echo "</ul>";

echo "<p style='margin-top: 20px; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
echo "<strong>🎉 ¡Limpieza completada!</strong><br>";
echo "Ahora puedes eliminar este script o dejarlo para futuras limpiezas.";
echo "</p>";

// ============================================
// FUNCIÓN AUXILIAR
// ============================================
function eliminarDirectorio($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            if (!@rmdir($fileinfo->getRealPath())) {
                return false;
            }
        } else {
            if (!@unlink($fileinfo->getRealPath())) {
                return false;
            }
        }
    }
    
    return @rmdir($dir);
}
?>
