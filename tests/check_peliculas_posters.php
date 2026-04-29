<?php
require_once __DIR__ . "/../config/conexion.php";

echo "=== VERIFICANDO POSTERS DE PELICULAS ===\n\n";

$stmt = $pdo->query("SELECT id, titulo, poster FROM pelicula LIMIT 3");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . "\n";
    echo "Titulo: " . $row['titulo'] . "\n";
    echo "Poster: " . $row['poster'] . "\n";
    echo "---\n";
}
