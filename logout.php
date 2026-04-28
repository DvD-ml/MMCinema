<?php
session_start();
require_once "config/conexion.php";
require_once "helpers/Auth.php";
require_once "helpers/Logger.php";

// Cerrar sesión y eliminar cookies
Auth::cerrarSesion($pdo);

header("Location: index.php");
exit();
?>