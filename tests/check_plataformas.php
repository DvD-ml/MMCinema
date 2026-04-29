<?php
require_once __DIR__ . "/../config/conexion.php";

echo "=== VERIFICANDO LOGOS DE PLATAFORMAS ===\n\n";

$stmt = $pdo->query("SELECT id, nombre, logo FROM plataforma");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . "\n";
    echo "Nombre: " . $row['nombre'] . "\n";
    echo "Logo: " . $row['logo'] . "\n";
    echo "---\n";
}
