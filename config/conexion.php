<?php

// Cargar variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno solo si existe el archivo .env
$envPath = __DIR__ . '/..';
if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
}

// Cargar helpers
require_once __DIR__ . '/../helpers/Logger.php';

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
    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
        die("Error de conexión a BD: " . $e->getMessage());
    } else {
        die("Error de conexión. Por favor, intenta más tarde.");
    }
}

