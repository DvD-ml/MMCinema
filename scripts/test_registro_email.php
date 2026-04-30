<?php
/**
 * Script para probar registro y envío de email
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/mail.php';

echo "=== TEST DE REGISTRO Y EMAIL ===\n\n";

// Crear un usuario de prueba
$username = 'test_' . time();
$email = 'test_' . time() . '@example.com';
$password = password_hash('password123', PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));
$fechaActual = date('Y-m-d H:i:s');
$fechaExpira = date('Y-m-d H:i:s', strtotime('+1 day'));

echo "1. Creando usuario de prueba...\n";
echo "   Username: $username\n";
echo "   Email: $email\n";

$sql = "INSERT INTO usuario (username, email, password_hash, creado, verificado, token_verificacion, token_expira) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stm = $pdo->prepare($sql);

try {
    $stm->execute([$username, $email, $password, $fechaActual, 0, $token, $fechaExpira]);
    echo "   ✓ Usuario creado\n\n";
} catch (PDOException $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Agregar email a la cola
echo "2. Agregando email a la cola...\n";

$sql = "INSERT INTO email_queue (tipo, destinatario_email, destinatario_nombre, token) VALUES (?, ?, ?, ?)";
$stm = $pdo->prepare($sql);
$stm->execute(['verificacion', $email, $username, $token]);

echo "   ✓ Email agregado a la cola\n\n";

// Procesar la cola
echo "3. Procesando cola de emails...\n";

require_once __DIR__ . '/procesar_emails.php';

echo "   ✓ Cola procesada\n\n";

// Verificar estado
echo "4. Verificando estado del email...\n";

$sql = "SELECT enviado, intentos FROM email_queue WHERE destinatario_email = ? ORDER BY id DESC LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([$email]);
$result = $stm->fetch();

if ($result) {
    $estado = $result['enviado'] ? 'ENVIADO' : 'PENDIENTE';
    echo "   Estado: $estado\n";
    echo "   Intentos: " . $result['intentos'] . "\n";
} else {
    echo "   ✓ Email enviado y eliminado de la cola\n";
}

echo "\n=== FIN DEL TEST ===\n";
?>
