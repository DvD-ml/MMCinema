<?php
require_once 'config/conexion.php';

echo "=== LIMPIANDO BASE DE DATOS ===\n\n";

// Eliminar el slide que no tiene imagen
$sql = 'DELETE FROM carrusel_destacado WHERE id = 6';
$stmt = $pdo->prepare($sql);
$resultado = $stmt->execute();

if ($resultado) {
    echo "OK - Slide ID 6 eliminado\n";
} else {
    echo "ERROR - No se pudo eliminar el slide\n";
}

// Verificar estado final
$count = (int)$pdo->query("SELECT COUNT(*) FROM carrusel_destacado")->fetchColumn();
echo "Slides restantes en BD: $count\n";

echo "\n✅ Limpieza completada\n";
?>
