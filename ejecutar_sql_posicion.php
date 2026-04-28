<?php
/**
 * Script para agregar la columna imagen_posicion
 */

require_once "config/conexion.php";

echo "<h1>Agregar Campo de Posición de Imagen</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

try {
    echo "<h2>1. Verificando si la columna ya existe...</h2>";
    
    // Verificar si la columna existe
    $columns = $pdo->query("DESCRIBE carrusel_destacado")->fetchAll(PDO::FETCH_ASSOC);
    $existe_columna = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'imagen_posicion') {
            $existe_columna = true;
            break;
        }
    }
    
    if ($existe_columna) {
        echo "⚠️ La columna 'imagen_posicion' ya existe<br>";
    } else {
        echo "✅ La columna 'imagen_posicion' no existe, procediendo a crearla...<br>";
        
        // Agregar la columna
        $pdo->exec("ALTER TABLE carrusel_destacado ADD COLUMN imagen_posicion VARCHAR(50) DEFAULT 'center' AFTER imagen_fondo");
        echo "✅ Columna 'imagen_posicion' agregada correctamente<br>";
    }
    
    echo "<h2>2. Actualizando slides existentes...</h2>";
    
    // Actualizar slides que no tengan posición
    $stmt = $pdo->exec("UPDATE carrusel_destacado SET imagen_posicion = 'center' WHERE imagen_posicion IS NULL OR imagen_posicion = ''");
    echo "✅ Actualizados $stmt slides con posición por defecto 'center'<br>";
    
    echo "<h2>3. Verificando estructura final...</h2>";
    
    $columns = $pdo->query("DESCRIBE carrusel_destacado")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columnas de la tabla carrusel_destacado:<br>";
    foreach ($columns as $col) {
        $highlight = $col['Field'] === 'imagen_posicion' ? ' <strong style="color: #e50914;">← NUEVA</strong>' : '';
        echo "- {$col['Field']} ({$col['Type']}){$highlight}<br>";
    }
    
    echo "<h2>✅ Proceso Completado</h2>";
    echo "Ahora puedes:<br>";
    echo "1. <a href='admin/carrusel_destacado.php'>Ir al panel del carrusel</a><br>";
    echo "2. Editar un slide y seleccionar la posición de la imagen<br>";
    echo "3. <a href='index.php'>Ver el resultado en el home</a><br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>