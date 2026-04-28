<?php

// Intentar cargar variables de entorno
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad(); // safeLoad no lanza error si falla
} catch (Exception $e) {
    // Si falla, continuamos con valores por defecto
}

// Cargar helpers
require_once __DIR__ . '/../helpers/Logger.php';

// Configuración con valores por defecto
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'mmcinema3';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    Logger::debug("Conexión a BD establecida correctamente");
    
} catch (PDOException $e) {
    Logger::error("Error de conexión a BD", $e);
    
    // Mensaje genérico al usuario (no exponer detalles)
    $appEnv = $_ENV['APP_ENV'] ?? 'production';
    if ($appEnv === 'development') {
        die("Error de conexión a BD: " . $e->getMessage());
    } else {
        die("Error de conexión. Por favor, intenta más tarde.");
    }
}
