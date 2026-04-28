<?php
/**
 * Script para agregar las nuevas categorías al ENUM de carrusel_destacado
 * Ejecutar una sola vez desde: http://localhost/david/MMCINEMA/ejecutar_agregar_categorias.php
 */

require_once "config/conexion.php";

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='utf-8'>
    <title>Agregar Categorías - MMCinema</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #1a1a1a; color: #fff; }
        .success { background: #10b981; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .error { background: #ef4444; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .info { background: #3b82f6; padding: 15px; border-radius: 8px; margin: 10px 0; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 8px; overflow-x: auto; }
        h1 { color: #f97316; }
    </style>
</head>
<body>
    <h1>🔧 Agregar Nuevas Categorías al Carrusel</h1>";

try {
    echo "<div class='info'>📋 Verificando estructura actual...</div>";
    
    // Verificar estructura actual
    $stmt = $pdo->query("
        SELECT COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = 'mmcinema3' 
          AND TABLE_NAME = 'carrusel_destacado' 
          AND COLUMN_NAME = 'categoria'
    ");
    $columna_actual = $stmt->fetchColumn();
    
    echo "<div class='info'><strong>Estructura actual:</strong><pre>" . htmlspecialchars($columna_actual) . "</pre></div>";
    
    // Verificar si ya tiene las nuevas categorías
    if (strpos($columna_actual, 'nueva_temporada') !== false && strpos($columna_actual, 'nuevo_episodio') !== false) {
        echo "<div class='success'>✅ Las categorías 'nueva_temporada' y 'nuevo_episodio' ya existen en la base de datos.</div>";
        echo "<div class='info'>No es necesario ejecutar la actualización.</div>";
    } else {
        echo "<div class='info'>⚙️ Agregando nuevas categorías...</div>";
        
        // Ejecutar ALTER TABLE
        $sql = "ALTER TABLE `carrusel_destacado` 
                MODIFY COLUMN `categoria` ENUM(
                    'destacada',
                    'mejores',
                    'proximamente',
                    'nueva_temporada',
                    'nuevo_episodio',
                    'nuevos',
                    'populares'
                ) DEFAULT 'destacada'";
        
        $pdo->exec($sql);
        
        echo "<div class='success'>✅ Categorías agregadas exitosamente</div>";
        
        // Verificar nueva estructura
        $stmt = $pdo->query("
            SELECT COLUMN_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = 'mmcinema3' 
              AND TABLE_NAME = 'carrusel_destacado' 
              AND COLUMN_NAME = 'categoria'
        ");
        $columna_nueva = $stmt->fetchColumn();
        
        echo "<div class='success'><strong>Nueva estructura:</strong><pre>" . htmlspecialchars($columna_nueva) . "</pre></div>";
    }
    
    echo "<div class='info'>
        <h3>✅ Proceso completado</h3>
        <p>Ahora puedes:</p>
        <ul>
            <li>Ir al <a href='admin/carrusel_destacado.php' style='color: #f97316;'>Panel de Carrusel</a></li>
            <li>Editar un slide y seleccionar 'Nueva Temporada' o 'Nuevo Episodio'</li>
            <li>La fecha aparecerá centrada y grande en el carrusel del home</li>
        </ul>
        <p><strong>Nota:</strong> Puedes eliminar este archivo después de ejecutarlo.</p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>
        <h3>Solución alternativa:</h3>
        <p>Ejecuta este SQL manualmente en phpMyAdmin:</p>
        <pre>ALTER TABLE `carrusel_destacado` 
MODIFY COLUMN `categoria` ENUM(
    'destacada',
    'mejores',
    'proximamente',
    'nueva_temporada',
    'nuevo_episodio',
    'nuevos',
    'populares'
) DEFAULT 'destacada';</pre>
    </div>";
}

echo "</body></html>";
?>
