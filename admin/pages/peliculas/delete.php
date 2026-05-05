<?php
require_once "../../../auth.php";
require_once __DIR__ . "/../../../config/conexion.php";

// Configurar variables para CRUD genérico
$entity = 'pelicula';
$table = 'pelicula';
$redirect = 'peliculas.php';

// Incluir CRUD genérico
require_once __DIR__ . "/crud/delete.php";
?>





