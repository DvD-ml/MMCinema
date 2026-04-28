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
        echo '<li>' . $tabla . '</li>';
    }
    echo '</ul>';
    
} catch (PDOException $e) {
    echo '<p style="color: red;"><strong>❌ Error:</strong> ' . $e->getMessage() . '</p>';
}
?>
