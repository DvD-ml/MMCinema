<?php
echo "=== DIAGNÓSTICO COMPLETO DEL PROYECTO ===\n\n";

// 1. Verificar estructura de carpetas
echo "1. ESTRUCTURA DE CARPETAS\n";
echo "========================\n\n";

$carpetas_esperadas = [
    'admin',
    'assets',
    'assets/css',
    'assets/img',
    'assets/img/carrusel',
    'assets/img/logos',
    'assets/img/noticias',
    'assets/img/posters',
    'assets/img/series',
    'assets/js',
    'backend',
    'config',
    'database',
    'database/migrations',
    'docs',
    'helpers',
    'includes',
    'storage',
    'storage/tickets',
    'storage/logs',
    'storage/cache',
];

foreach ($carpetas_esperadas as $carpeta) {
    $ruta = __DIR__ . '/' . $carpeta;
    $existe = is_dir($ruta) ? '✅' : '❌';
    echo "$existe $carpeta\n";
}

// 2. Verificar archivos PHP principales
echo "\n\n2. ARCHIVOS PHP PRINCIPALES\n";
echo "============================\n\n";

$archivos_principales = [
    'index.php',
    'cartelera.php',
    'pelicula.php',
    'serie.php',
    'series.php',
    'proximamente.php',
    'noticias.php',
    'noticia.php',
    'criticas.php',
    'perfil.php',
    'login.php',
    'registro.php',
    'logout.php',
    'navbar.php',
    'footer.php',
    'laterales.php',
];

foreach ($archivos_principales as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    $existe = file_exists($ruta) ? '✅' : '❌';
    echo "$existe $archivo\n";
}

// 3. Verificar archivos CSS
echo "\n\n3. ARCHIVOS CSS\n";
echo "===============\n\n";

$css_dir = __DIR__ . '/assets/css';
if (is_dir($css_dir)) {
    $archivos_css = scandir($css_dir);
    foreach ($archivos_css as $archivo) {
        if ($archivo !== '.' && $archivo !== '..') {
            echo "✅ $archivo\n";
        }
    }
} else {
    echo "❌ Carpeta assets/css no existe\n";
}

// 4. Verificar imágenes
echo "\n\n4. IMÁGENES\n";
echo "===========\n\n";

$carpetas_img = [
    'assets/img/carrusel',
    'assets/img/logos',
    'assets/img/noticias',
    'assets/img/posters',
    'assets/img/series',
];

foreach ($carpetas_img as $carpeta) {
    $ruta = __DIR__ . '/' . $carpeta;
    if (is_dir($ruta)) {
        $archivos = scandir($ruta);
        $count = count($archivos) - 2; // Restar . y ..
        echo "📁 $carpeta: $count archivos\n";
    } else {
        echo "❌ $carpeta no existe\n";
    }
}

// 5. Verificar conexión a BD
echo "\n\n5. CONEXIÓN A BASE DE DATOS\n";
echo "===========================\n\n";

try {
    require_once 'config/conexion.php';
    echo "✅ Conexión a BD exitosa\n";
    
    // Verificar tablas
    $sql = "SHOW TABLES";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tablas encontradas: " . count($tablas) . "\n";
    foreach ($tablas as $tabla) {
        echo "  - $tabla\n";
    }
    
    // Verificar carrusel
    echo "\nSlides en carrusel_destacado: ";
    $count = (int)$pdo->query("SELECT COUNT(*) FROM carrusel_destacado")->fetchColumn();
    echo "$count\n";
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
}

// 6. Verificar rutas en archivos
echo "\n\n6. VERIFICACIÓN DE RUTAS EN ARCHIVOS\n";
echo "====================================\n\n";

$index_file = __DIR__ . '/index.php';
if (file_exists($index_file)) {
    $contenido = file_get_contents($index_file);
    
    // Buscar referencias a CSS
    if (strpos($contenido, 'assets/css/styles.css') !== false) {
        echo "✅ index.php: Ruta CSS correcta (assets/css/styles.css)\n";
    } else {
        echo "❌ index.php: Ruta CSS incorrecta\n";
    }
    
    // Buscar referencias a imágenes del carrusel
    if (strpos($contenido, 'assets/img/carrusel/') !== false) {
        echo "✅ index.php: Ruta carrusel correcta (assets/img/carrusel/)\n";
    } else {
        echo "❌ index.php: Ruta carrusel incorrecta\n";
    }
}

// 7. Resumen final
echo "\n\n7. RESUMEN FINAL\n";
echo "================\n\n";

$problemas = [];

// Verificar carpetas críticas
$carpetas_criticas = ['admin', 'assets', 'config', 'helpers', 'includes'];
foreach ($carpetas_criticas as $carpeta) {
    if (!is_dir(__DIR__ . '/' . $carpeta)) {
        $problemas[] = "Falta carpeta: $carpeta";
    }
}

// Verificar archivos críticos
$archivos_criticos = ['index.php', 'config/conexion.php', 'admin/index.php'];
foreach ($archivos_criticos as $archivo) {
    if (!file_exists(__DIR__ . '/' . $archivo)) {
        $problemas[] = "Falta archivo: $archivo";
    }
}

if (empty($problemas)) {
    echo "✅ No se encontraron problemas críticos\n";
} else {
    echo "❌ Problemas encontrados:\n";
    foreach ($problemas as $problema) {
        echo "  - $problema\n";
    }
}

echo "\n✅ Diagnóstico completado\n";
?>
