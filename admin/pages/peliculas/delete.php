<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

// Configurar variables para CRUD genérico
$entity = 'pelicula';
$table = 'pelicula';
$redirect = 'list.php';

// Incluir CRUD genérico
require_once __DIR__ . "/../../../admin/crud/delete.php";
?>





