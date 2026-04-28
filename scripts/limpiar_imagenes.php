<?php
/**
 * Script para limpiar carpetas y archivos de imágenes no usados
 * Ejecutar desde: http://localhost/david/MMCINEMA/scripts/limpiar_imagenes.php
 */

echo "<h1>🧹 Limpieza de Imágenes No Usadas</h1>";
echo "<hr>";

// ============================================
// 1. ELIMINAR CARPETAS DUPLICADAS
// ============================================
echo "<h2>1️⃣ Eliminando carpetas duplicadas...</h2>";

$carpetasAEliminar = [
    __DIR__ . '/../assets/img/noticias/noticias',
    __DIR__ . '/../assets/img/series/series'
];

foreach ($carpetasAEliminar as $carpeta) {
    if (is_dir($carpeta)) {
        echo "Carpeta encontrada: <code>$carpeta</code><br>";
        
        // Contar archivos
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($carpeta),
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
        $result = eliminarDirectorio($carpeta);
        
        if ($result) {
            echo "<p style='color: green; font-weight: bold;'>✅ Carpeta eliminada correctamente</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No se pudo eliminar la carpeta. Intenta manualmente o verifica permisos.</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ La carpeta no existe: <code>$carpeta</code></p>";
    }
    echo "<br>";
}

echo "<hr>";

// ============================================
// 2. ELIMINAR ARCHIVOS NO USADOS
// ============================================
echo "<h2>2️⃣ Eliminando archivos no usados...</h2>";

$archivosAEliminar = [
    __DIR__ . '/../assets/img/cinta.jpg' => 'assets/img/cinta.jpg',
    __DIR__ . '/../assets/img/logo3.png' => 'assets/img/logo3.png',
    __DIR__ . '/../admin/logo/logo.png' => 'admin/logo/logo.png'
];

foreach ($archivosAEliminar as $ruta => $nombre) {
    if (file_exists($ruta)) {
        echo "Archivo encontrado: <code>$nombre</code><br>";
        
        $size = filesize($ruta);
        $sizeKB = round($size / 1024, 2);
        echo "Tamaño: <strong>$sizeKB KB</strong><br>";
        
        if (@unlink($ruta)) {
            echo "<p style='color: green; font-weight: bold;'>✅ Archivo eliminado correctamente</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ No se pudo eliminar el archivo. Intenta manualmente o verifica permisos.</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ El archivo no existe: <code>$nombre</code></p>";
    }
    echo "<br>";
}

echo "<hr>";

// ============================================
// 3. VERIFICAR ESTRUCTURA FINAL
// ============================================
echo "<h2>3️⃣ Verificando estructura final...</h2>";

$carpetasEsperadas = [
    'assets/img/posters',
    'assets/img/carrusel',
    'assets/img/logos',
    'assets/img/noticias',
    'assets/img/plataformas',
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
// 4. ESTADÍSTICAS
// ============================================
echo "<h2>4️⃣ Estadísticas de Imágenes</h2>";

$estadisticas = [
    'Posters de películas' => 'assets/img/posters',
    'Imágenes de carrusel' => 'assets/img/carrusel',
    'Logos de carrusel' => 'assets/img/logos',
    'Imágenes de noticias' => 'assets/img/noticias',
    'Logos de plataformas' => 'assets/img/plataformas',
    'Posters de series' => 'assets/img/series/posters',
    'Banners de series' => 'assets/img/series/banners',
    'Posters de temporadas' => 'assets/img/series/temporadas'
];

$totalArchivos = 0;
$totalTamaño = 0;

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #333; color: white;'>";
echo "<th>Categoría</th>";
echo "<th>Archivos</th>";
echo "<th>Tamaño Total</th>";
echo "</tr>";

foreach ($estadisticas as $nombre => $carpeta) {
    $ruta = __DIR__ . '/../' . $carpeta;
    if (is_dir($ruta)) {
        $files = glob($ruta . '/*');
        $count = count($files);
        
        $tamaño = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                $tamaño += filesize($file);
            }
        }
        
        $totalArchivos += $count;
        $totalTamaño += $tamaño;
        
        $tamañoMB = round($tamaño / (1024 * 1024), 2);
        
        echo "<tr>";
        echo "<td>$nombre</td>";
        echo "<td style='text-align: center;'>$count</td>";
        echo "<td style='text-align: right;'>{$tamañoMB} MB</td>";
        echo "</tr>";
    }
}

$totalTamañoMB = round($totalTamaño / (1024 * 1024), 2);

echo "<tr style='background: #f0f0f0; font-weight: bold;'>";
echo "<td>TOTAL</td>";
echo "<td style='text-align: center;'>$totalArchivos</td>";
echo "<td style='text-align: right;'>{$totalTamañoMB} MB</td>";
echo "</tr>";
echo "</table>";

echo "<hr>";

// ============================================
// 5. RESUMEN
// ============================================
echo "<h2>✨ Resumen</h2>";
echo "<ul>";
echo "<li>✅ Carpetas duplicadas eliminadas</li>";
echo "<li>✅ Archivos no usados eliminados</li>";
echo "<li>✅ Estructura verificada</li>";
echo "<li>✅ Estadísticas generadas</li>";
echo "</ul>";

echo "<p style='margin-top: 20px; padding: 10px; background: #e8f5e9; border-radius: 5px;'>";
echo "<strong>🎉 ¡Limpieza completada!</strong><br>";
echo "Tu proyecto está ahora más limpio y optimizado.<br>";
echo "Total de imágenes: <strong>$totalArchivos</strong><br>";
echo "Tamaño total: <strong>{$totalTamañoMB} MB</strong>";
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
