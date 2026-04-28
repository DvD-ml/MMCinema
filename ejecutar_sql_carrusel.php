<?php
/**
 * Script para ejecutar el SQL del carrusel destacado
 */

require_once "config/conexion.php";

try {
    // Leer el archivo SQL
    $sql_content = file_get_contents('sql/create_carrusel_destacado.sql');
    
    if ($sql_content === false) {
        throw new Exception("No se pudo leer el archivo SQL");
    }
    
    // Separar las consultas por punto y coma
    $queries = explode(';', $sql_content);
    
    $executed = 0;
    $errors = [];
    
    foreach ($queries as $query) {
        $query = trim($query);
        
        // Saltar consultas vacías o comentarios
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($query);
            $executed++;
            echo "✓ Consulta ejecutada correctamente\n";
        } catch (PDOException $e) {
            // Si es error de tabla ya existe, no es crítico
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Tabla ya existe, continuando...\n";
            } else {
                $errors[] = "Error en consulta: " . $e->getMessage();
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n=== RESUMEN ===\n";
    echo "Consultas ejecutadas: $executed\n";
    
    if (empty($errors)) {
        echo "✓ Todas las consultas se ejecutaron correctamente\n";
        echo "✓ La tabla 'carrusel_destacado' está lista para usar\n";
        echo "✓ Puedes acceder al panel de admin en: /admin/carrusel_destacado.php\n";
    } else {
        echo "Errores encontrados:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error fatal: " . $e->getMessage() . "\n";
}
?>