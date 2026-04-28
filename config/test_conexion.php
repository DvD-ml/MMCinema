<?php
/**
 * Script de prueba de conexión a la base de datos
 * Accede a: http://localhost/MMCinema/config/test_conexion.php
 */

require_once __DIR__ . '/conexion.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - MMCinema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
        }
        .info {
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🎬 MMCinema - Test de Conexión</h1>
        
        <?php
        try {
            // Test de conexión
            $stmt = $pdo->query("SELECT VERSION() as version");
            $version = $stmt->fetch();
            
            // Contar tablas
            $stmt = $pdo->query("SHOW TABLES");
            $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo '<p class="success">✅ Conexión exitosa a la base de datos</p>';
            
            echo '<div class="info">';
            echo '<h3>Información de la Base de Datos:</h3>';
            echo '<table>';
            echo '<tr><th>Parámetro</th><th>Valor</th></tr>';
            echo '<tr><td>Host</td><td>' . ($_ENV['DB_HOST'] ?? 'localhost') . '</td></tr>';
            echo '<tr><td>Base de Datos</td><td>' . ($_ENV['DB_NAME'] ?? 'mmcinema3') . '</td></tr>';
            echo '<tr><td>Usuario</td><td>' . ($_ENV['DB_USER'] ?? 'root') . '</td></tr>';
            echo '<tr><td>Versión MySQL</td><td>' . htmlspecialchars($version['version']) . '</td></tr>';
            echo '<tr><td>Tablas encontradas</td><td>' . count($tablas) . '</td></tr>';
            echo '</table>';
            echo '</div>';
            
            if (count($tablas) > 0) {
                echo '<div class="info" style="margin-top: 20px;">';
                echo '<h3>Tablas en la Base de Datos:</h3>';
                echo '<ul>';
                foreach ($tablas as $tabla) {
                    // Contar registros
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `$tabla`");
                    $count = $stmt->fetch();
                    echo '<li><strong>' . htmlspecialchars($tabla) . '</strong> (' . $count['total'] . ' registros)</li>';
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="info" style="margin-top: 20px; background: #fff3cd; border-left-color: #ffc107;">';
                echo '<p>⚠️ <strong>Advertencia:</strong> No se encontraron tablas en la base de datos.</p>';
                echo '<p>Necesitas importar el esquema SQL para crear las tablas.</p>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<p style="color: #dc3545; font-size: 18px;">❌ Error de conexión</p>';
            echo '<div class="info" style="background: #f8d7da; border-left-color: #dc3545;">';
            echo '<p><strong>Mensaje de error:</strong></p>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p><a href="../index.php" style="color: #007bff; text-decoration: none;">← Volver al inicio</a></p>
        </div>
    </div>
</body>
</html>
