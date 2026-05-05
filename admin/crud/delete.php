<?php
/**
 * CRUD Delete Genérico
 * 
 * Variables requeridas:
 * - $entity: nombre de la entidad (pelicula, noticia, etc)
 * - $table: nombre de la tabla en BD
 * - $redirect: página a redirigir después de eliminar
 * - $pdo: conexión a BD
 * 
 * Funciones opcionales:
 * - $beforeDelete: función a ejecutar antes de eliminar
 * - $afterDelete: función a ejecutar después de eliminar
 * - $checkDependencies: función para verificar dependencias
 */

require_once __DIR__ . "/../auth.php";
verificarAuth();
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/../../helpers/CSRF.php";

// Validar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $redirect");
    exit();
}

// Validar variables requeridas
if (!isset($entity, $table, $redirect)) {
    die("Error: Variables requeridas no definidas");
}

// Validar token CSRF
CSRF::validarOAbortar();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: $redirect?error=1");
    exit();
}

// Verificar que el registro existe
$stm = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stm->execute([$id]);
$record = $stm->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    header("Location: $redirect?error=1");
    exit();
}

// Verificar dependencias si existe función
if (isset($checkDependencies) && is_callable($checkDependencies)) {
    $dependencyError = $checkDependencies($id, $pdo);
    if ($dependencyError) {
        header("Location: $redirect?error=$dependencyError");
        exit();
    }
}

// Ejecutar función beforeDelete si existe
if (isset($beforeDelete) && is_callable($beforeDelete)) {
    $beforeDelete($record, $pdo);
}

try {
    // Eliminar registro
    $sql = "DELETE FROM $table WHERE id = ?";
    $stm = $pdo->prepare($sql);
    $stm->execute([$id]);
    
    // Ejecutar función afterDelete si existe
    if (isset($afterDelete) && is_callable($afterDelete)) {
        $afterDelete($record, $pdo);
    }
    
    header("Location: $redirect?ok=1");
    exit();
    
} catch (Exception $e) {
    header("Location: $redirect?error=1");
    exit();
}
?>
