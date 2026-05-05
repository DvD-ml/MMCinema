<?php
require_once "../../../auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Configurar variables para CRUD genérico
$entity = 'proyeccion';
$table = 'proyeccion';
$fields = ['id_pelicula', 'fecha', 'hora', 'sala'];
$redirect = 'list.php';
$optionalFields = [];

// Función para validar datos específicos de proyecciones
$beforeSave = function(&$data, $pdo) {
    $id_pelicula = (int)$data['id_pelicula'];
    $sala = trim($data['sala']);
    
    // Validar que la película existe
    $stm = $pdo->prepare("SELECT id FROM pelicula WHERE id = ?");
    $stm->execute([$id_pelicula]);
    if (!$stm->fetch()) {
        header("Location: form.php?pelicula_id=" . $id_pelicula . "&error=1");
        exit();
    }
    
    // Validar que la sala existe
    $stm = $pdo->prepare("SELECT sala FROM sala_config WHERE sala = ?");
    $stm->execute([$sala]);
    if (!$stm->fetch()) {
        header("Location: form.php?pelicula_id=" . $id_pelicula . "&error=1");
        exit();
    }
};

// Incluir CRUD genérico
require_once __DIR__ . "/crud/save.php";
?>






