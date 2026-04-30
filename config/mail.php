<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno solo si existe el archivo .env
$envPath = __DIR__ . '/..';
if (file_exists($envPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
}

// Cargar helpers
require_once __DIR__ . '/../helpers/Logger.php';

define('MM_BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/david/MMCINEMA');
define('MM_SENDGRID_API_KEY', $_ENV['SENDGRID_API_KEY'] ?? '');

function mm_enviar_con_sendgrid(string $destinatarioEmail, string $destinatarioNombre, string $asunto, string $htmlBody, string $textBody = ''): bool
{
    $apiKey = MM_SENDGRID_API_KEY;
    
    if (empty($apiKey)) {
        return false;
    }

    $fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'noreply@mmcinema.com';
    $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'MMCinema';

    $payload = [
        'personalizations' => [
            [
                'to' => [
                    [
                        'email' => $destinatarioEmail,
                        'name' => $destinatarioNombre
                    ]
                ]
            ]
        ],
        'from' => [
            'email' => $fromEmail,
            'name' => $fromName
        ],
        'subject' => $asunto,
        'content' => [
            [
                'type' => 'text/html',
                'value' => $htmlBody
            ]
        ]
    ];

    if (!empty($textBody)) {
        $payload['content'][] = [
            'type' => 'text/plain',
            'value' => $textBody
        ];
    }

    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 202;
}

function mm_configurar_mailer(): PHPMailer
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'] ?? throw new Exception('MAIL_USERNAME no configurado en .env');
    $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? throw new Exception('MAIL_PASSWORD no configurado en .env');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($_ENV['MAIL_PORT'] ?? 587);
    $mail->CharSet    = 'UTF-8';

    $fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? $_ENV['MAIL_USERNAME'] ?? 'david.monzonlopez@gmail.com';
    $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'MMCinema';
    
    $mail->setFrom($fromEmail, $fromName);

    return $mail;
}

function mm_obtener_logo_correo(): array
{
    $rutaLogo = __DIR__ . '/../assets/img/logo2.png';

    if (file_exists($rutaLogo)) {
        return [$rutaLogo, true];
    }

    return ['', false];
}

function enviarCorreoVerificacion(string $destinatarioEmail, string $destinatarioNombre, string $token): bool
{
    // Emails deshabilitados - servidor no permite conexiones salientes
    return true;
}

function enviarCorreoBienvenida(string $destinatarioEmail, string $destinatarioNombre): bool
{
    // Emails deshabilitados - servidor no permite conexiones salientes
    return true;
}

function enviarCorreoEntrada(
    string $destinatarioEmail,
    string $destinatarioNombre,
    array $ticket,
    string $rutaPdf
): bool {
    // Emails deshabilitados - servidor no permite conexiones salientes
    return true;
}

function enviarCorreoRecuperacion(string $destinatarioEmail, string $destinatarioNombre, string $token): bool
{
    // Emails deshabilitados - servidor no permite conexiones salientes
    return true;
}
