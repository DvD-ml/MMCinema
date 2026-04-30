<?php
/**
 * Test de conexión SMTP a Gmail
 */

echo "=== TEST DE CONEXIÓN SMTP A GMAIL ===\n\n";

// Cargar variables de entorno
require_once __DIR__ . '/../config/conexion.php';

$host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
$port = (int)($_ENV['MAIL_PORT'] ?? 587);
$username = $_ENV['MAIL_USERNAME'] ?? '';
$password = $_ENV['MAIL_PASSWORD'] ?? '';

echo "Host: $host\n";
echo "Port: $port\n";
echo "Username: $username\n";
echo "Password: " . (strlen($password) > 0 ? "***" : "NO CONFIGURADA") . "\n\n";

// Intentar conexión
echo "Intentando conectar a $host:$port...\n";

$socket = @fsockopen($host, $port, $errno, $errstr, 10);

if (!$socket) {
    echo "✗ Error de conexión: $errstr (Código: $errno)\n";
    exit(1);
}

echo "✓ Conexión establecida\n\n";

// Leer respuesta del servidor
$response = fgets($socket, 1024);
echo "Respuesta del servidor: $response\n";

// Enviar EHLO
echo "\nEnviando EHLO...\n";
fputs($socket, "EHLO localhost\r\n");
$response = fgets($socket, 1024);
echo "Respuesta: $response\n";

// Leer más líneas de respuesta
while (strpos($response, '250 ') !== 0) {
    $response = fgets($socket, 1024);
    echo "Respuesta: $response";
}

// Enviar STARTTLS
echo "\nEnviando STARTTLS...\n";
fputs($socket, "STARTTLS\r\n");
$response = fgets($socket, 1024);
echo "Respuesta: $response\n";

// Intentar habilitar encriptación
if (function_exists('stream_context_create')) {
    echo "\nIntentando habilitar TLS...\n";
    $context = stream_context_create(['ssl' => ['verify_peer' => false]]);
    if (stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        echo "✓ TLS habilitado\n";
    } else {
        echo "✗ Error al habilitar TLS\n";
    }
}

// Intentar autenticación
echo "\nIntentando autenticación...\n";
$auth_string = base64_encode($username . "\0" . $username . "\0" . $password);
fputs($socket, "AUTH PLAIN " . $auth_string . "\r\n");
$response = fgets($socket, 1024);
echo "Respuesta: $response\n";

if (strpos($response, '235') !== false) {
    echo "✓ Autenticación exitosa\n";
} else {
    echo "✗ Error de autenticación\n";
}

fclose($socket);

echo "\n=== FIN DEL TEST ===\n";
?>
