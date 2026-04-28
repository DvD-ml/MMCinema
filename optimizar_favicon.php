<?php
/**
 * Script para optimizar el favicon y crear múltiples tamaños
 * Ejecutar una sola vez desde: http://localhost/david/MMCINEMA/optimizar_favicon.php
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
    <title>Optimizar Favicon - MMCinema</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { background: #10b981; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .error { background: #ef4444; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .info { background: #3b82f6; padding: 15px; border-radius: 8px; margin: 10px 0; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 8px; overflow-x: auto; }
        h1 { color: #f97316; }
        img { border: 2px solid #f97316; border-radius: 8px; margin: 10px; }
    </style>
</head>
<body>
    <h1>🎨 Optimizar Favicon</h1>";

try {
    $favicon_original = 'favicon.png';
    
    if (!file_exists($favicon_original)) {
        throw new Exception("No se encontró el archivo favicon.png");
    }
    
    echo "<div class='info'>📋 Favicon original encontrado</div>";
    
    // Obtener información del favicon original
    $info = getimagesize($favicon_original);
    echo "<div class='info'><strong>Tamaño original:</strong> {$info[0]}x{$info[1]} px</div>";
    echo "<div class='info'><strong>Peso original:</strong> " . number_format(filesize($favicon_original) / 1024, 2) . " KB</div>";
    
    // Cargar imagen original
    $imagen_original = imagecreatefrompng($favicon_original);
    
    if (!$imagen_original) {
        throw new Exception("No se pudo cargar la imagen PNG");
    }
    
    // Crear favicon de 32x32 (tamaño estándar para navegadores)
    $favicon_32 = imagecreatetruecolor(32, 32);
    imagealphablending($favicon_32, false);
    imagesavealpha($favicon_32, true);
    $transparent = imagecolorallocatealpha($favicon_32, 0, 0, 0, 127);
    imagefill($favicon_32, 0, 0, $transparent);
    imagecopyresampled($favicon_32, $imagen_original, 0, 0, 0, 0, 32, 32, $info[0], $info[1]);
    
    // Guardar favicon de 32x32
    imagepng($favicon_32, 'favicon-32x32.png', 9);
    echo "<div class='success'>✅ Creado favicon-32x32.png (" . number_format(filesize('favicon-32x32.png') / 1024, 2) . " KB)</div>";
    
    // Crear favicon de 16x16 (tamaño para pestañas)
    $favicon_16 = imagecreatetruecolor(16, 16);
    imagealphablending($favicon_16, false);
    imagesavealpha($favicon_16, true);
    $transparent = imagecolorallocatealpha($favicon_16, 0, 0, 0, 127);
    imagefill($favicon_16, 0, 0, $transparent);
    imagecopyresampled($favicon_16, $imagen_original, 0, 0, 0, 0, 16, 16, $info[0], $info[1]);
    
    // Guardar favicon de 16x16
    imagepng($favicon_16, 'favicon-16x16.png', 9);
    echo "<div class='success'>✅ Creado favicon-16x16.png (" . number_format(filesize('favicon-16x16.png') / 1024, 2) . " KB)</div>";
    
    // Crear apple-touch-icon de 180x180
    $apple_icon = imagecreatetruecolor(180, 180);
    imagealphablending($apple_icon, false);
    imagesavealpha($apple_icon, true);
    $transparent = imagecolorallocatealpha($apple_icon, 0, 0, 0, 127);
    imagefill($apple_icon, 0, 0, $transparent);
    imagecopyresampled($apple_icon, $imagen_original, 0, 0, 0, 0, 180, 180, $info[0], $info[1]);
    
    // Guardar apple-touch-icon
    imagepng($apple_icon, 'apple-touch-icon.png', 9);
    echo "<div class='success'>✅ Creado apple-touch-icon.png (" . number_format(filesize('apple-touch-icon.png') / 1024, 2) . " KB)</div>";
    
    // Liberar memoria
    imagedestroy($imagen_original);
    imagedestroy($favicon_32);
    imagedestroy($favicon_16);
    imagedestroy($apple_icon);
    
    echo "<div class='info'>
        <h3>📝 Instrucciones</h3>
        <p>Ahora debes agregar estas líneas en el &lt;head&gt; de tus páginas HTML:</p>
        <pre>&lt;link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"favicon-32x32.png\"&gt;
&lt;link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"favicon-16x16.png\"&gt;
&lt;link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"apple-touch-icon.png\"&gt;</pre>
        <p><strong>Nota:</strong> Puedes eliminar este archivo después de ejecutarlo.</p>
    </div>";
    
    echo "<div class='info'>
        <h3>🖼️ Vista previa de los favicons creados:</h3>
        <div style='background: #fff; padding: 20px; border-radius: 8px; display: inline-block;'>
            <img src='favicon-32x32.png' alt='32x32' style='width: 32px; height: 32px;'>
            <img src='favicon-16x16.png' alt='16x16' style='width: 16px; height: 16px;'>
            <img src='apple-touch-icon.png' alt='180x180' style='width: 60px; height: 60px;'>
        </div>
        <p><small>32x32 | 16x16 | 180x180 (Apple)</small></p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</body></html>";
?>
