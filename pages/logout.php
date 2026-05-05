<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../helpers/Auth.php";
require_once __DIR__ . "/../helpers/Logger.php";

// Cerrar sesión y eliminar cookies
Auth::cerrarSesion($pdo);

header("Location: index.php");
exit();
?>