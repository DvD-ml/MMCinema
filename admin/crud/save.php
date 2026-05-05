<?php
/**
 * CRUD Save Genérico
 * 
 * Variables requeridas:
 * - $entity: nombre de la entidad (pelicula, noticia, etc)
 * - $table: nombre de la tabla en BD
 * - $fields: array de campos del formulario
 * - $redirect: página a redirigir después de guardar
 * - $pdo: conexión a BD
 * 
 * Funciones opcionales:
 * - $beforeSave: función a ejecutar antes de guardar
 * - $afterSave: función a ejecutar después de guardar
 * - $optionalFields: array de campos opcionales
 */

require_once __DIR__ . "/../auth.php";
verificarAuth();
require_once __DIR__ . "/../../config/conexion.php";
require_once __DIR__ . "/../../helpers/CSRF.php";

// Validar variables requeridas
if (!isset($entity, $table, $fields, $redirect)) {
    die("Error: Variables requeridas no definidas");
}

// Validar token CSRF (solo una vez)
CSRF::validarOAbortar();

// Inicializar optionalFields si no está definido
if (!isset($optionalFields)) {
    $optionalFields = [];
}

$id = (int)($_POST['id'] ?? 0);
$action = $id > 0 ? 'updated' : 'created';
$data = [];
$errors = [];

// Procesar campos
foreach ($fields as $field) {
    $value = $_POST[$field] ?? '';
    
    // Validar campos requeridos (excepto IDs y campos opcionales)
    if (empty($value) && strpos($field, 'id_') !== 0 && !in_array($field, $optionalFields)) {
        $errors[] = "El campo " . str_replace('_', ' ', $field) . " es requerido";
    }
    
    $data[$field] = $value;
}

// Si hay errores, redirigir con error
if (!empty($errors)) {
    header("Location: form.php?id=$id&error=1");
    exit();
}

// Ejecutar función beforeSave si existe
if (isset($beforeSave) && is_callable($beforeSave)) {
    $beforeSave($data, $pdo);
}

try {
    if ($id > 0) {
        // Actualizar
        $setClauses = [];
        $values = [];
        
        foreach ($fields as $field) {
            $setClauses[] = "$field = ?";
            $values[] = $data[$field];
        }
        
        $values[] = $id;
        
        $sql = "UPDATE $table SET " . implode(", ", $setClauses) . " WHERE id = ?";
        $stm = $pdo->prepare($sql);
        $stm->execute($values);
    } else {
        // Insertar
        $columns = implode(", ", $fields);
        $placeholders = implode(", ", array_fill(0, count($fields), "?"));
        $values = array_values($data);
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stm = $pdo->prepare($sql);
        $stm->execute($values);
    }
    
    // Ejecutar función afterSave si existe
    if (isset($afterSave) && is_callable($afterSave)) {
        $afterSave($data, $pdo);
    }
    
    header("Location: $redirect?ok=$action");
    exit();
    
} catch (Exception $e) {
    // Log error for debugging
    error_log("CRUD Error in $entity: " . $e->getMessage());
    header("Location: form.php?id=$id&error=1");
    exit();
}
?>
