<?php
require_once 'config/conexion.php';

echo "=== VERIFICANDO SLIDE EN CARRUSEL ===\n\n";

$sql = 'SELECT * FROM carrusel_destacado';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total de slides: " . count($slides) . "\n\n";

foreach ($slides as $slide) {
    echo "ID: " . $slide['id'] . "\n";
    echo "Titulo: " . $slide['titulo'] . "\n";
    echo "Tipo: " . $slide['tipo'] . "\n";
    echo "ID Contenido: " . $slide['id_contenido'] . "\n";
    echo "Imagen Fondo: " . $slide['imagen_fondo'] . "\n";
    echo "Logo: " . $slide['logo_titulo'] . "\n";
    echo "Categoria: " . $slide['categoria'] . "\n";
    echo "Activo: " . $slide['activo'] . "\n";
    echo "Orden: " . $slide['orden'] . "\n";
    echo "\n";
    
    if (!empty($slide['imagen_fondo'])) {
        $ruta_archivo = __DIR__ . '/assets/img/carrusel/' . $slide['imagen_fondo'];
        $existe = file_exists($ruta_archivo) ? 'SI' : 'NO';
        echo "Existe archivo? " . $existe . "\n";
        echo "Ruta esperada: " . $ruta_archivo . "\n";
    }
    echo "\n---\n\n";
}
?>
