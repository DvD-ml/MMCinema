<?php
/**
 * Script para debuggear SendGrid
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/mail.php';

echo "=== DEBUG SENDGRID ===\n\n";

// Verificar API key
echo "1. Verificando API key...\n";
$apiKey = MM_SENDGRID_API_KEY;
echo "   API Key: " . (empty($apiKey) ? "NO CONFIGURADA" : substr($apiKey, 0, 10) . "...") . "\n";

if (empty($apiKey)) {
    echo "   ✗ API Key no configurada\n";
    exit(1);
}

echo "   ✓ API Key configurada\n\n";

// Intentar enviar un email de prueba
echo "2. Intentando enviar email de prueba...\n";

$payload = [
    'personalizations' => [
        [
            'to' => [
                [
                    'email' => 'dml.procv@gmail.com',
                    'name' => 'Test User'
                ]
            ]
        ]
    ],
    'from' => [
        'email' => 'noreply@mmcinema.com',
        'name' => 'MMCinema'
    ],
    'subject' => 'Test Email from SendGrid',
    'content' => [
        [
            'type' => 'text/html',
            'value' => '<h1>Test Email</h1><p>This is a test email from SendGrid API.</p>'
        ]
    ]
];

echo "   Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init('https://api.sendgrid.com/v3/mail/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

echo "3. Respuesta de SendGrid...\n";
echo "   HTTP Code: $httpCode\n";
echo "   Response: $response\n";

if (!empty($curlError)) {
    echo "   Curl Error: $curlError\n";
}

curl_close($ch);

if ($httpCode === 202) {
    echo "\n   ✓ Email enviado correctamente\n";
} else {
    echo "\n   ✗ Error al enviar email\n";
}

echo "\n=== FIN DEL DEBUG ===\n";
?>
