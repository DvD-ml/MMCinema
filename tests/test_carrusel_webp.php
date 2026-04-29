<?php
/**
 * Script de prueba para verificar el sistema de conversión WebP del carrusel
 */

require_once "config/conexion.php";
require_once "includes/optimizar_imagen.php";

echo "<h1>Test - Sistema WebP del Carrusel</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

echo "<h2>1. Verificación de Carpetas</h2>";

$carpetas = [
    'img/carrusel/' => 'Imágenes de fondo del carrusel',
    'img/logos/' => 'Logos PNG para superponer'
];

foreach ($carpetas as $carpeta => $descripcion) {
    if (is_dir($carpeta)) {
        $permisos = substr(sprintf('%o', fileperms($carpeta)), -4);
        echo "✅ $carpeta existe (permisos: $permisos) - $descripcion<br>";
        
        // Verificar si es escribible
        if (is_writable($carpeta)) {
            echo "✅ $carpeta es escribible<br>";
        } else {
            echo "❌ $carpeta NO es escribible<br>";
        }
        
        // Listar archivos existentes
        $archivos = glob($carpeta . '*');
        if (empty($archivos)) {
            echo "ℹ️ $carpeta está vacía<br>";
        } else {
            echo "ℹ️ $carpeta contiene " . count($archivos) . " archivos<br>";
        }
        
    } else {
        echo "❌ $carpeta NO existe - $descripcion<br>";
        
        // Intentar crear la carpeta
        if (mkdir($carpeta, 0777, true)) {
            echo "✅ $carpeta creada exitosamente<br>";
        } else {
            echo "❌ No se pudo crear $carpeta<br>";
        }
    }
    echo "<br>";
}

echo "<h2>2. Verificación de Funciones WebP</h2>";

// Verificar funciones GD
$funciones_gd = [
    'imagecreatefromjpeg' => 'Leer imágenes JPEG',
    'imagecreatefrompng' => 'Leer imágenes PNG',
    'imagecreatefromwebp' => 'Leer imágenes WebP',
    'imagewebp' => 'Crear imágenes WebP',
    'imagecreatetruecolor' => 'Crear lienzo',
    'imagecopyresampled' => 'Redimensionar imágenes'
];

foreach ($funciones_gd as $funcion => $descripcion) {
    if (function_exists($funcion)) {
        echo "✅ $funcion() disponible - $descripcion<br>";
    } else {
        echo "❌ $funcion() NO disponible - $descripcion<br>";
    }
}

// Verificar AVIF si está disponible
if (function_exists('imagecreatefromavif')) {
    echo "✅ imagecreatefromavif() disponible - Soporte AVIF<br>";
} else {
    echo "⚠️ imagecreatefromavif() NO disponible - Sin soporte AVIF (opcional)<br>";
}

echo "<h2>3. Verificación de Archivo optimizar_imagen.php</h2>";

if (file_exists('includes/optimizar_imagen.php')) {
    echo "✅ includes/optimizar_imagen.php existe<br>";
    
    // Verificar funciones principales
    $funciones_requeridas = [
        'mm_slug_nombre_archivo',
        'mm_borrar_archivo_si_existe', 
        'mm_crear_imagen_desde_archivo',
        'optimizarYGuardarWebp'
    ];
    
    foreach ($funciones_requeridas as $funcion) {
        if (function_exists($funcion)) {
            echo "✅ Función $funcion() disponible<br>";
        } else {
            echo "❌ Función $funcion() NO disponible<br>";
        }
    }
} else {
    echo "❌ includes/optimizar_imagen.php NO existe<br>";
}

echo "<h2>4. Test de Conversión (Simulado)</h2>";

// Simular datos de archivo
$archivo_simulado = [
    'name' => 'test-carrusel.jpg',
    'type' => 'image/jpeg',
    'size' => 1024000, // 1MB
    'tmp_name' => '/tmp/test',
    'error' => UPLOAD_ERR_OK
];

echo "Archivo simulado:<br>";
echo "- Nombre: {$archivo_simulado['name']}<br>";
echo "- Tipo: {$archivo_simulado['type']}<br>";
echo "- Tamaúo: " . number_format($archivo_simulado['size'] / 1024, 2) . " KB<br>";

// Test de slug
if (function_exists('mm_slug_nombre_archivo')) {
    $slug = mm_slug_nombre_archivo('Test Carrusel Úoúo');
    echo "- Slug generado: '$slug'<br>";
}

echo "<h2>5. Verificación de Tabla carrusel_destacado</h2>";

try {
    $result = $pdo->query("SHOW TABLES LIKE 'carrusel_destacado'");
    if ($result->rowCount() > 0) {
        echo "✅ Tabla 'carrusel_destacado' existe<br>";
        
        $count = $pdo->query("SELECT COUNT(*) FROM carrusel_destacado")->fetchColumn();
        echo "✅ Registros en la tabla: $count<br>";
    } else {
        echo "❌ Tabla 'carrusel_destacado' NO existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error verificando tabla: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Resumen</h2>";
echo "El sistema de conversión WebP para el carrusel está configurado para:<br>";
echo "• Convertir automáticamente todas las imágenes a WebP<br>";
echo "• Optimizar el tamaúo y calidad de las imágenes<br>";
echo "• Generar nombres únicos para evitar conflictos<br>";
echo "• Eliminar automáticamente imágenes anteriores al actualizar<br>";
echo "• Soportar formatos: JPG, PNG, WebP, AVIF<br>";
echo "<br>";
echo "Configuración específica del carrusel:<br>";
echo "• Fondos: máximo 1920x1080px, calidad 85%<br>";
echo "• Logos: máximo 800x300px, calidad 90%<br>";
echo "<br>";
echo "<a href='admin/carrusel_destacado.php'>Ir al Panel del Carrusel</a><br>";
?>