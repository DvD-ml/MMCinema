<?php
/**
 * Script de Verificación del Carrusel
 * Verifica que todos los archivos y configuraciones estén correctos
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Verificación Carrusel MMCinema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #0f172a;
            color: #fff;
        }
        h1 {
            color: #f97316;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }
        h2 {
            color: #fbbf24;
            margin-top: 30px;
        }
        .check {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            border-left: 4px solid #10b981;
        }
        .error {
            border-left-color: #ef4444;
        }
        .warning {
            border-left-color: #f59e0b;
        }
        .success {
            color: #10b981;
            font-weight: bold;
        }
        .fail {
            color: #ef4444;
            font-weight: bold;
        }
        .info {
            color: #60a5fa;
        }
        code {
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 4px;
            color: #fbbf24;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 5px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        .btn:hover {
            background: #ea580c;
        }
        .btn-secondary {
            background: #6b7280;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>";

echo "<h1>🔍 Verificación del Carrusel MMCinema</h1>";
echo "<p>Fecha: " . date('d/m/Y H:i:s') . "</p>";

// Verificar conexión a BD
echo "<h2>1. Conexión a Base de Datos</h2>";
try {
    require_once "config/conexion.php";
    echo "<div class='check'><span class='success'>✅ CORRECTO:</span> Conexión a base de datos establecida</div>";
} catch (Exception $e) {
    echo "<div class='check error'><span class='fail'>❌ ERROR:</span> No se pudo conectar a la base de datos: " . $e->getMessage() . "</div>";
    exit;
}

// Verificar tabla carrusel_destacado
echo "<h2>2. Tabla carrusel_destacado</h2>";
try {
    $stmt = $pdo->query("DESCRIBE carrusel_destacado");
    $columnas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $columnasRequeridas = ['id', 'titulo', 'tipo', 'id_contenido', 'imagen_fondo', 'imagen_posicion', 'logo_titulo', 'categoria', 'descripcion', 'activo', 'orden'];
    $faltantes = array_diff($columnasRequeridas, $columnas);
    
    if (empty($faltantes)) {
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> Tabla <code>carrusel_destacado</code> existe con todas las columnas necesarias</div>";
        echo "<div class='check'><span class='info'>📋 Columnas:</span> " . implode(', ', $columnas) . "</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> Faltan columnas: " . implode(', ', $faltantes) . "</div>";
    }
} catch (Exception $e) {
    echo "<div class='check error'><span class='fail'>❌ ERROR:</span> Tabla no existe o hay un problema: " . $e->getMessage() . "</div>";
}

// Verificar carpetas
echo "<h2>3. Carpetas de Imágenes</h2>";
$carpetas = [
    'img/carrusel' => 'Imágenes de fondo del carrusel',
    'img/logos' => 'Logos PNG transparentes'
];

foreach ($carpetas as $carpeta => $descripcion) {
    if (is_dir($carpeta)) {
        $archivos = glob($carpeta . '/*.{jpg,jpeg,png,webp,avif}', GLOB_BRACE);
        $count = count($archivos);
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> Carpeta <code>$carpeta</code> existe ($count archivos) - $descripcion</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> Carpeta <code>$carpeta</code> no existe - $descripcion</div>";
    }
}

// Verificar archivos PHP
echo "<h2>4. Archivos PHP Necesarios</h2>";
$archivos = [
    'admin/carrusel_destacado.php' => 'Panel de administración del carrusel',
    'includes/optimizar_imagen.php' => 'Función de conversión WebP con transparencia',
    'index.php' => 'Página principal con carrusel'
];

foreach ($archivos as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        $size = filesize($archivo);
        $modified = date('d/m/Y H:i:s', filemtime($archivo));
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> <code>$archivo</code> existe (" . number_format($size) . " bytes, modificado: $modified) - $descripcion</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> <code>$archivo</code> no existe - $descripcion</div>";
    }
}

// Verificar archivos CSS
echo "<h2>5. Archivos CSS</h2>";
$cssFiles = [
    'css/home.css' => 'Estilos del carrusel Netflix',
    'css/admin.css' => 'Estilos del panel admin'
];

foreach ($cssFiles as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        $size = filesize($archivo);
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> <code>$archivo</code> existe (" . number_format($size) . " bytes) - $descripcion</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> <code>$archivo</code> no existe - $descripcion</div>";
    }
}

// Verificar slides activos
echo "<h2>6. Slides del Carrusel</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM carrusel_destacado WHERE activo = 1");
    $slidesActivos = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM carrusel_destacado");
    $slidesTotal = $stmt->fetchColumn();
    
    if ($slidesActivos > 0) {
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> Hay <strong>$slidesActivos</strong> slides activos de un total de <strong>$slidesTotal</strong></div>";
        
        // Mostrar detalles de los slides
        $stmt = $pdo->query("SELECT id, titulo, tipo, categoria, imagen_fondo, logo_titulo, activo FROM carrusel_destacado ORDER BY orden ASC");
        $slides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='check'><span class='info'>📋 Slides configurados:</span><ul>";
        foreach ($slides as $slide) {
            $estado = $slide['activo'] ? '<span class="success">Activo</span>' : '<span style="color: #6b7280;">Inactivo</span>';
            $logo = $slide['logo_titulo'] ? '✅ Con logo' : '❌ Sin logo';
            echo "<li><strong>{$slide['titulo']}</strong> ({$slide['tipo']}) - {$slide['categoria']} - $estado - $logo</li>";
        }
        echo "</ul></div>";
    } else {
        echo "<div class='check warning'><span style='color: #f59e0b;'>⚠️ ADVERTENCIA:</span> No hay slides activos. Total de slides: <strong>$slidesTotal</strong></div>";
    }
} catch (Exception $e) {
    echo "<div class='check error'><span class='fail'>❌ ERROR:</span> No se pudieron consultar los slides: " . $e->getMessage() . "</div>";
}

// Verificar función de transparencia
echo "<h2>7. Función de Transparencia WebP</h2>";
if (file_exists('includes/optimizar_imagen.php')) {
    $contenido = file_get_contents('includes/optimizar_imagen.php');
    
    if (strpos($contenido, 'imagealphablending') !== false && strpos($contenido, 'imagesavealpha') !== false) {
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> La función <code>optimizarYGuardarWebp()</code> incluye código para preservar transparencia</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> La función no incluye código para preservar transparencia. Actualizar el archivo.</div>";
    }
} else {
    echo "<div class='check error'><span class='fail'>❌ ERROR:</span> Archivo <code>includes/optimizar_imagen.php</code> no encontrado</div>";
}

// Verificar extensiones PHP
echo "<h2>8. Extensiones PHP</h2>";
$extensiones = ['gd', 'pdo_mysql'];
foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='check'><span class='success'>✅ CORRECTO:</span> Extensión <code>$ext</code> está cargada</div>";
    } else {
        echo "<div class='check error'><span class='fail'>❌ ERROR:</span> Extensión <code>$ext</code> no está cargada</div>";
    }
}

// Verificar soporte WebP
echo "<h2>9. Soporte de Formatos de Imagen</h2>";
if (function_exists('imagewebp')) {
    echo "<div class='check'><span class='success'>✅ CORRECTO:</span> PHP soporta conversión a WebP</div>";
} else {
    echo "<div class='check error'><span class='fail'>❌ ERROR:</span> PHP no soporta conversión a WebP. Actualizar GD library.</div>";
}

if (function_exists('imagecreatefromavif')) {
    echo "<div class='check'><span class='success'>✅ CORRECTO:</span> PHP soporta lectura de AVIF</div>";
} else {
    echo "<div class='check warning'><span style='color: #f59e0b;'>⚠️ ADVERTENCIA:</span> PHP no soporta AVIF (opcional)</div>";
}

// Enlaces útiles
echo "<h2>10. Enlaces Útiles</h2>";
echo "<div class='check'>";
echo "<a href='index.php' class='btn'>🏠 Ver Home con Carrusel</a>";
echo "<a href='admin/carrusel_destacado.php' class='btn'>⚙️ Panel Admin Carrusel</a>";
echo "<a href='admin/index.php' class='btn btn-secondary'>📊 Panel Admin Principal</a>";
echo "</div>";

echo "<hr style='margin: 40px 0; border-color: rgba(255,255,255,0.1);'>";
echo "<p style='text-align: center; color: #6b7280;'>MMCinema - Sistema de Carrusel Netflix © 2026</p>";

echo "</body></html>";
?>
