<?php
/**
 * Script simple para probar envío de emails
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/mail.php';

echo "=== TEST DE ENVÍO DE EMAIL ===\n\n";

// Probar envío directo
echo "1. Intentando enviar email de prueba...\n";

$resultado = enviarCorreoVerificacion(
    'david.monzonlopez@gmail.com',
    'Test User',
    'token_prueba_12345'
);

if ($resultado) {
    echo "✓ Email enviado correctamente\n";
} else {
    echo "✗ Error al enviar email\n";
}

echo "\n2. Verificando cola de emails...\n";

$sql = "SELECT COUNT(*) as total FROM email_queue";
$stm = $pdo->prepare($sql);
$stm->execute();
$result = $stm->fetch();

echo "Total de emails en cola: " . $result['total'] . "\n";

echo "\n3. Últimos 5 emails en cola:\n";

$sql = "SELECT id, tipo, destinatario_email, enviado, intentos, creado FROM email_queue ORDER BY id DESC LIMIT 5";
$stm = $pdo->prepare($sql);
$stm->execute();
$emails = $stm->fetchAll();

foreach ($emails as $email) {
    $estado = $email['enviado'] ? 'ENVIADO' : 'PENDIENTE';
    echo "ID: {$email['id']} | Tipo: {$email['tipo']} | Email: {$email['destinatario_email']} | Estado: $estado | Intentos: {$email['intentos']}\n";
}

echo "\n=== FIN DEL TEST ===\n";
?>
