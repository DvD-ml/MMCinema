<?php
echo '<h1>Test de Conexión Directa</h1>';

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=mmcinema3;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo '<p style="color: green;"><strong>✅ Conexión exitosa a la base de datos</strong></p>';
    
    $stmt = $pdo->query('SHOW TABLES');
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo '<p><strong>Tablas encontradas:</strong> ' . count($tablas) . '</p>';
    echo '<ul>';
    foreach ($tablas as $tabla) {
        $stmtCount = $pdo->query("SELECT COUNT(*) FROM `$tabla`");
        $count = $stmtCount->fetchColumn();
        echo '<li><strong>' . $tabla . '</strong> (' . $count . ' registros)</li>';
    }
    echo '</ul>';
    
    echo '<hr>';
    echo '<p><a href="index.php">Ir al inicio</a> | <a href="admin/">Ir al admin</a></p>';
    
} catch (PDOException $e) {
    echo '<p style="color: red;"><strong>❌ Error:</strong> ' . $e->getMessage() . '</p>';
    echo '<p>Verifica que:</p>';
    echo '<ul>';
    echo '<li>MySQL esté corriendo en XAMPP</li>';
    echo '<li>La base de datos "mmcinema3" exista</li>';
    echo '<li>El usuario sea "root" sin contraseña</li>';
    echo '</ul>';
}
?>
