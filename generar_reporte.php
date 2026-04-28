<?php
// Este script genera un reporte del estado del proyecto

$reporte = "=== DIAGNÓSTICO COMPLETO DEL PROYECTO ===\n\n";
$reporte .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
$reporte .= "Ruta: " . __DIR__ . "\n\n";

// 1. Verificar estructura de carpetas
$reporte .= "1. ESTRUCTURA DE CARPETAS\n";
$reporte .= "========================\n\n";

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
    $existe = is_dir($ruta) ? 'OK' : 'FALTA';
    $reporte .= "$existe - $carpeta\n";
}

// 2. Verificar archivos PHP principales
$reporte .= "\n\n2. ARCHIVOS PHP PRINCIPALES\n";
$reporte .= "============================\n\n";

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
    $existe = file_exists($ruta) ? 'OK' : 'FALTA';
    $reporte .= "$existe - $archivo\n";
}

// 3. Verificar archivos CSS
$reporte .= "\n\n3. ARCHIVOS CSS\n";
$reporte .= "===============\n\n";

$css_dir = __DIR__ . '/assets/css';
if (is_dir($css_dir)) {
    $archivos_css = scandir($css_dir);
    $count = 0;
    foreach ($archivos_css as $archivo) {
        if ($archivo !== '.' && $archivo !== '..') {
            $reporte .= "OK - $archivo\n";
            $count++;
        }
    }
    $reporte .= "Total: $count archivos\n";
} else {
    $reporte .= "FALTA - Carpeta assets/css\n";
}

// 4. Verificar imágenes
$reporte .= "\n\n4. IMÁGENES\n";
$reporte .= "===========\n\n";

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
        $reporte .= "OK - $carpeta: $count archivos\n";
    } else {
        $reporte .= "FALTA - $carpeta\n";
    }
}

// 5. Verificar conexión a BD
$reporte .= "\n\n5. CONEXIÓN A BASE DE DATOS\n";
$reporte .= "===========================\n\n";

try {
    require_once 'config/conexion.php';
    $reporte .= "OK - Conexión a BD exitosa\n";
    
    // Verificar tablas
    $sql = "SHOW TABLES";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $reporte .= "Tablas encontradas: " . count($tablas) . "\n";
    
    // Verificar carrusel
    $count = (int)$pdo->query("SELECT COUNT(*) FROM carrusel_destacado")->fetchColumn();
    $reporte .= "Slides en carrusel_destacado: $count\n";
    
} catch (Exception $e) {
    $reporte .= "ERROR - Conexión a BD: " . $e->getMessage() . "\n";
}

// Guardar reporte
$archivo_reporte = __DIR__ . '/REPORTE_DIAGNOSTICO.txt';
file_put_contents($archivo_reporte, $reporte);

echo $reporte;
echo "\n✅ Reporte guardado en: REPORTE_DIAGNOSTICO.txt\n";
?>
