<?php
echo "=== ANÁLISIS DE CARPETAS EN XAMPP ===\n\n";

$base_dir = __DIR__;

// Función para listar carpetas y archivos recursivamente
function listar_estructura($dir, $nivel = 0, $max_nivel = 3) {
    $items = [];
    
    if (!is_dir($dir)) {
        return $items;
    }
    
    $archivos = scandir($dir);
    
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        
        $ruta_completa = $dir . DIRECTORY_SEPARATOR . $archivo;
        $indent = str_repeat("  ", $nivel);
        
        if (is_dir($ruta_completa)) {
            $items[] = $indent . "📁 " . $archivo;
            
            if ($nivel < $max_nivel) {
                $subitems = listar_estructura($ruta_completa, $nivel + 1, $max_nivel);
                $items = array_merge($items, $subitems);
            }
        } else {
            $size = filesize($ruta_completa);
            $size_kb = round($size / 1024, 2);
            $items[] = $indent . "📄 " . $archivo . " (" . $size_kb . " KB)";
        }
    }
    
    return $items;
}

// Listar estructura
$estructura = listar_estructura($base_dir, 0, 2);

foreach ($estructura as $item) {
    echo $item . "\n";
}

echo "\n\n=== BÚSQUEDA DE DUPLICADOS ===\n\n";

// Buscar carpetas duplicadas
$carpetas_encontradas = [];

function buscar_carpetas($dir, &$carpetas, $nivel = 0) {
    if ($nivel > 3) return;
    
    $archivos = scandir($dir);
    
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        
        $ruta_completa = $dir . DIRECTORY_SEPARATOR . $archivo;
        
        if (is_dir($ruta_completa)) {
            $carpetas[$archivo][] = $ruta_completa;
            buscar_carpetas($ruta_completa, $carpetas, $nivel + 1);
        }
    }
}

buscar_carpetas($base_dir, $carpetas_encontradas);

// Mostrar duplicados
$duplicados = [];
foreach ($carpetas_encontradas as $nombre => $rutas) {
    if (count($rutas) > 1) {
        $duplicados[$nombre] = $rutas;
    }
}

if (empty($duplicados)) {
    echo "✅ No se encontraron carpetas duplicadas\n";
} else {
    echo "❌ Carpetas duplicadas encontradas:\n\n";
    foreach ($duplicados as $nombre => $rutas) {
        echo "Carpeta: $nombre\n";
        foreach ($rutas as $ruta) {
            echo "  - $ruta\n";
        }
        echo "\n";
    }
}

echo "\n=== ARCHIVOS DUPLICADOS ===\n\n";

// Buscar archivos duplicados
$archivos_encontrados = [];

function buscar_archivos($dir, &$archivos, $nivel = 0) {
    if ($nivel > 3) return;
    
    $items = scandir($dir);
    
    foreach ($items as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        
        $ruta_completa = $dir . DIRECTORY_SEPARATOR . $archivo;
        
        if (is_file($ruta_completa)) {
            $archivos[$archivo][] = $ruta_completa;
        } elseif (is_dir($ruta_completa)) {
            buscar_archivos($ruta_completa, $archivos, $nivel + 1);
        }
    }
}

buscar_archivos($base_dir, $archivos_encontrados);

// Mostrar duplicados
$archivos_duplicados = [];
foreach ($archivos_encontrados as $nombre => $rutas) {
    if (count($rutas) > 1) {
        $archivos_duplicados[$nombre] = $rutas;
    }
}

if (empty($archivos_duplicados)) {
    echo "✅ No se encontraron archivos duplicados\n";
} else {
    echo "❌ Archivos duplicados encontrados:\n\n";
    foreach ($archivos_duplicados as $nombre => $rutas) {
        echo "Archivo: $nombre\n";
        foreach ($rutas as $ruta) {
            echo "  - $ruta\n";
        }
        echo "\n";
    }
}

echo "\n✅ Análisis completado\n";
?>
