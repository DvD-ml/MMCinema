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
define('MM_EMAIL_IMAGE_BASE_URL', $_ENV['EMAIL_IMAGE_BASE_URL'] ?? MM_BASE_URL);
define('MM_SENDGRID_API_KEY', $_ENV['SENDGRID_API_KEY'] ?? '');
define('MM_MAIL_TIMEOUT', max(3, (int)($_ENV['MAIL_TIMEOUT'] ?? 8)));

function mm_url_absoluta(string $path): string
{
    return rtrim(MM_BASE_URL, '/') . '/' . ltrim($path, '/');
}

function mm_url_imagen_correo(string $path): string
{
    return rtrim(MM_EMAIL_IMAGE_BASE_URL, '/') . '/' . ltrim($path, '/');
}

function mm_ruta_publica_imagen_correo(string $localPath): ?string
{
    if (!is_file($localPath)) {
        return null;
    }

    $cacheDir = __DIR__ . '/../assets/img/email-cache';
    if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true) && !is_dir($cacheDir)) {
        return null;
    }

    $cacheName = 'mail_' . md5($localPath . '|' . filemtime($localPath)) . '.jpg';
    $cachePath = $cacheDir . '/' . $cacheName;

    if (!is_file($cachePath)) {
        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $img = match ($ext) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($localPath) : false,
            'png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($localPath) : false,
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($localPath) : false,
            default => false,
        };

        if (!$img) {
            return null;
        }

        $width = imagesx($img);
        $height = imagesy($img);
        $canvas = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($canvas, 17, 24, 39);
        imagefill($canvas, 0, 0, $bg);

        if ($ext === 'png') {
            imagealphablending($img, true);
        }

        imagecopy($canvas, $img, 0, 0, 0, 0, $width, $height);
        imagejpeg($canvas, $cachePath, 88);
        imagedestroy($img);
        imagedestroy($canvas);
    }

    return is_file($cachePath) ? mm_url_imagen_correo('assets/img/email-cache/' . $cacheName) : null;
}

function mm_logo_url_correo(): string
{
    $candidatos = [
        __DIR__ . '/../assets/img/logo2.png',
        __DIR__ . '/../assets/img/logo.png',
    ];

    foreach ($candidatos as $rutaLogo) {
        $url = mm_ruta_publica_imagen_correo($rutaLogo);
        if ($url !== null) {
            return $url;
        }

        if (is_file($rutaLogo)) {
            return mm_url_imagen_correo('assets/img/' . basename($rutaLogo));
        }
    }

    return mm_url_imagen_correo('assets/img/logo2.png');
}

function mm_poster_url_correo(?string $poster): ?string
{
    $poster = trim((string)$poster);
    if ($poster === '') {
        return null;
    }

    $candidatos = [];
    $normalizado = str_replace('\\', '/', $poster);
    $candidatos[] = __DIR__ . '/../assets/img/posters/' . basename($normalizado);

    if (str_starts_with($normalizado, 'assets/')) {
        $candidatos[] = __DIR__ . '/../' . $normalizado;
    }

    foreach ($candidatos as $rutaPoster) {
        $url = mm_ruta_publica_imagen_correo($rutaPoster);
        if ($url !== null) {
            return $url;
        }

        if (is_file($rutaPoster)) {
            return mm_url_imagen_correo('assets/img/posters/' . basename($rutaPoster));
        }
    }

    return null;
}

function mm_tipo_mime_archivo(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return match ($ext) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        default => 'application/octet-stream',
    };
}

function mm_adjunto_inline(string $path, string $contentId, ?string $filename = null): array
{
    return [
        'path' => $path,
        'filename' => $filename ?? basename($path),
        'type' => mm_tipo_mime_archivo($path),
        'disposition' => 'inline',
        'content_id' => $contentId,
    ];
}

function mm_preparar_imagen_email(string $path): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if ($ext !== 'webp' || !function_exists('imagecreatefromwebp') || !function_exists('imagejpeg')) {
        return $path;
    }

    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mm_mail_' . md5($path . '|' . filemtime($path)) . '.jpg';
    if (is_file($tmp)) {
        return $tmp;
    }

    $img = @imagecreatefromwebp($path);
    if (!$img) {
        return $path;
    }

    imagejpeg($img, $tmp, 88);
    imagedestroy($img);

    return is_file($tmp) ? $tmp : $path;
}

function mm_adjunto_logo_correo(): array
{
    $candidatos = [
        __DIR__ . '/../assets/img/logo2.png',
        __DIR__ . '/../assets/img/logo.png',
    ];

    foreach ($candidatos as $rutaLogo) {
        if (is_file($rutaLogo)) {
            return [mm_adjunto_inline(mm_preparar_imagen_email($rutaLogo), 'mmcinema_logo', 'mmcinema-logo.png')];
        }
    }

    return [];
}

function mm_adjunto_poster_correo(?string $poster): array
{
    $poster = trim((string)$poster);
    if ($poster === '') {
        return [];
    }

    $rutaPoster = __DIR__ . '/../assets/img/posters/' . basename($poster);
    if (!is_file($rutaPoster)) {
        return [];
    }

    $rutaPosterEmail = mm_preparar_imagen_email($rutaPoster);
    return [mm_adjunto_inline($rutaPosterEmail, 'mmcinema_poster', pathinfo($rutaPoster, PATHINFO_FILENAME) . '.jpg')];
}

function mm_agregar_adjuntos_mailer(PHPMailer $mail, array $adjuntos): void
{
    foreach ($adjuntos as $adjunto) {
        if (empty($adjunto['path']) || !is_file($adjunto['path'])) {
            continue;
        }

        if (($adjunto['disposition'] ?? 'attachment') === 'inline' && !empty($adjunto['content_id'])) {
            $mail->addEmbeddedImage(
                $adjunto['path'],
                $adjunto['content_id'],
                $adjunto['filename'] ?? basename($adjunto['path']),
                'base64',
                $adjunto['type'] ?? mm_tipo_mime_archivo($adjunto['path'])
            );
            continue;
        }

        $mail->addAttachment($adjunto['path'], $adjunto['filename'] ?? '');
    }
}

function mm_html_base(string $titulo, string $contenido): string
{
    $logoUrl = mm_logo_url_correo();

    return '<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><title>' . htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') . '</title></head>
<body style="margin:0;padding:0;background:#0b1220;font-family:Arial,sans-serif;color:#111827;">
    <div style="max-width:620px;margin:0 auto;padding:28px 16px;">
        <div style="background:#111827;border-radius:16px;padding:24px;color:#f9fafb;">
            <div style="text-align:center;margin-bottom:22px;">
                <img src="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '" alt="MMCinema" style="display:inline-block;max-width:150px;height:auto;border:0;outline:none;text-decoration:none;">
            </div>
            ' . $contenido . '
            <p style="margin-top:28px;color:#9ca3af;font-size:13px;line-height:1.5;">Si no esperabas este correo, puedes ignorarlo.</p>
        </div>
    </div>
</body>
</html>';
}

function mm_enviar_con_sendgrid(string $destinatarioEmail, string $destinatarioNombre, string $asunto, string $htmlBody, string $textBody = '', array $adjuntos = []): bool
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
        'content' => []
    ];

    if (!empty($textBody)) {
        $payload['content'][] = [
            'type' => 'text/plain',
            'value' => $textBody
        ];
    }

    $payload['content'][] = [
        'type' => 'text/html',
        'value' => $htmlBody
    ];

    foreach ($adjuntos as $adjunto) {
        if (empty($adjunto['path']) || !is_file($adjunto['path'])) {
            continue;
        }

        $payload['attachments'][] = [
            'content' => base64_encode(file_get_contents($adjunto['path'])),
            'filename' => $adjunto['filename'] ?? basename($adjunto['path']),
            'type' => $adjunto['type'] ?? 'application/pdf',
            'disposition' => $adjunto['disposition'] ?? 'attachment'
        ];

        if (!empty($adjunto['content_id'])) {
            $payload['attachments'][array_key_last($payload['attachments'])]['content_id'] = $adjunto['content_id'];
        }
    }

    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, MM_MAIL_TIMEOUT);
    curl_setopt($ch, CURLOPT_TIMEOUT, MM_MAIL_TIMEOUT);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    $enviado = $httpCode === 202;
    if (!$enviado) {
        Logger::error('SendGrid no acepto el correo', null, [
            'http_code' => $httpCode,
            'response' => $response,
            'curl_error' => $curlError,
            'to' => $destinatarioEmail
        ]);
    }

    return $enviado;
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
    $mail->Timeout    = MM_MAIL_TIMEOUT;
    $mail->SMTPKeepAlive = false;

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
    $url = mm_url_absoluta('pages/verificar_email.php?token=' . urlencode($token));
    $asunto = 'Verifica tu cuenta en MMCinema';
    $html = mm_html_base($asunto, '
        <h1 style="margin:0 0 12px;font-size:24px;color:#f9fafb;">Verifica tu cuenta</h1>
        <p style="color:#d1d5db;line-height:1.6;">Hola ' . htmlspecialchars($destinatarioNombre, ENT_QUOTES, 'UTF-8') . ', confirma tu correo para activar tu cuenta en MMCinema.</p>
        <p style="text-align:center;margin:28px 0;">
            <a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#f59e0b;color:#111827;text-decoration:none;font-weight:bold;padding:13px 18px;border-radius:10px;">Verificar cuenta</a>
        </p>
        <p style="color:#9ca3af;font-size:13px;line-height:1.5;">El enlace caduca en 24 horas. Si el boton no funciona, abre este enlace:<br>' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</p>
    ');
    $texto = "Hola $destinatarioNombre,\n\nVerifica tu cuenta en MMCinema:\n$url\n\nEl enlace caduca en 24 horas.";

    $adjuntos = [];

    if (MM_SENDGRID_API_KEY !== '') {
        return mm_enviar_con_sendgrid($destinatarioEmail, $destinatarioNombre, $asunto, $html, $texto, $adjuntos);
    }

    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);
        $mail->Subject = $asunto;
        $mail->isHTML(true);
        $mail->Body = $html;
        $mail->AltBody = $texto;
        mm_agregar_adjuntos_mailer($mail, $adjuntos);
        return $mail->send();
    } catch (Throwable $e) {
        Logger::error('No se pudo enviar correo de verificacion', $e, ['to' => $destinatarioEmail]);
        return false;
    }
}

function enviarCorreoBienvenida(string $destinatarioEmail, string $destinatarioNombre): bool
{
    $asunto = 'Bienvenido a MMCinema';
    $html = mm_html_base($asunto, '
        <h1 style="margin:0 0 12px;font-size:24px;color:#f9fafb;">Bienvenido a MMCinema</h1>
        <p style="color:#d1d5db;line-height:1.6;">Hola ' . htmlspecialchars($destinatarioNombre, ENT_QUOTES, 'UTF-8') . ', tu cuenta ya esta activa. Ya puedes reservar entradas, guardar favoritos y publicar criticas.</p>
        <p style="text-align:center;margin:28px 0;">
            <a href="' . htmlspecialchars(mm_url_absoluta('pages/login.php'), ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#f59e0b;color:#111827;text-decoration:none;font-weight:bold;padding:13px 18px;border-radius:10px;">Entrar en MMCinema</a>
        </p>
    ');
    $texto = "Hola $destinatarioNombre,\n\nTu cuenta de MMCinema ya esta activa.";

    $adjuntos = [];

    if (MM_SENDGRID_API_KEY !== '') {
        return mm_enviar_con_sendgrid($destinatarioEmail, $destinatarioNombre, $asunto, $html, $texto, $adjuntos);
    }

    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);
        $mail->Subject = $asunto;
        $mail->isHTML(true);
        $mail->Body = $html;
        $mail->AltBody = $texto;
        mm_agregar_adjuntos_mailer($mail, $adjuntos);
        return $mail->send();
    } catch (Throwable $e) {
        Logger::error('No se pudo enviar correo de bienvenida', $e, ['to' => $destinatarioEmail]);
        return false;
    }
}

function enviarCorreoEntrada(
    string $destinatarioEmail,
    string $destinatarioNombre,
    array $ticket,
    string $rutaPdf
): bool {
    $asunto = 'Tu entrada para ' . ($ticket['titulo'] ?? 'MMCinema');
    $posterHtml = '';
    $posterUrl = mm_poster_url_correo($ticket['poster'] ?? '');
    $adjuntos = [];

    if ($posterUrl !== null) {
        $posterHtml = '
            <td style="width:150px;padding:0 18px 0 0;vertical-align:top;">
                <img src="' . htmlspecialchars($posterUrl, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($ticket['titulo'] ?? 'Poster', ENT_QUOTES, 'UTF-8') . '" style="display:block;width:132px;max-width:132px;border-radius:10px;border:1px solid #374151;height:auto;">
            </td>';
    }

    $html = mm_html_base($asunto, '
        <h1 style="margin:0 0 12px;font-size:24px;color:#f9fafb;">Entrada confirmada</h1>
        <p style="color:#d1d5db;line-height:1.6;">Hola ' . htmlspecialchars($destinatarioNombre, ENT_QUOTES, 'UTF-8') . ', tu reserva se ha generado correctamente.</p>
        <div style="background:#0b1220;border:1px solid #374151;border-radius:12px;padding:16px;margin-top:18px;color:#e5e7eb;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr>
                    ' . $posterHtml . '
                    <td style="vertical-align:top;color:#e5e7eb;">
                        <p style="margin:0 0 12px;"><strong>Pelicula:</strong> ' . htmlspecialchars($ticket['titulo'] ?? '', ENT_QUOTES, 'UTF-8') . '</p>
                        <p style="margin:0 0 12px;"><strong>Fecha:</strong> ' . htmlspecialchars($ticket['fecha'] ?? '', ENT_QUOTES, 'UTF-8') . ' <strong>Hora:</strong> ' . htmlspecialchars($ticket['hora'] ?? '', ENT_QUOTES, 'UTF-8') . '</p>
                        <p style="margin:0 0 12px;"><strong>Sala:</strong> ' . htmlspecialchars($ticket['sala'] ?? '', ENT_QUOTES, 'UTF-8') . '</p>
                        <p style="margin:0 0 12px;"><strong>Asientos:</strong> ' . htmlspecialchars($ticket['asientos'] ?? '', ENT_QUOTES, 'UTF-8') . '</p>
                        <p style="margin:0 0 12px;"><strong>Total:</strong> ' . htmlspecialchars((string)($ticket['total'] ?? ''), ENT_QUOTES, 'UTF-8') . ' EUR</p>
                        <p style="margin:0;"><strong>Codigo:</strong> ' . htmlspecialchars($ticket['codigo'] ?? '', ENT_QUOTES, 'UTF-8') . '</p>
                    </td>
                </tr>
            </table>
        </div>
    ');
    $texto = "Entrada confirmada\n\nPelicula: " . ($ticket['titulo'] ?? '') . "\nFecha: " . ($ticket['fecha'] ?? '') . "\nHora: " . ($ticket['hora'] ?? '') . "\nSala: " . ($ticket['sala'] ?? '') . "\nAsientos: " . ($ticket['asientos'] ?? '') . "\nCodigo: " . ($ticket['codigo'] ?? '');

    if (MM_SENDGRID_API_KEY !== '') {
        if ($rutaPdf !== '' && is_file($rutaPdf)) {
            $adjuntos[] = [
                'path' => $rutaPdf,
                'filename' => 'ticket_' . ($ticket['codigo'] ?? 'mmcinema') . '.pdf',
                'type' => 'application/pdf'
            ];
        }

        return mm_enviar_con_sendgrid($destinatarioEmail, $destinatarioNombre, $asunto, $html, $texto, $adjuntos);
    }

    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);
        $mail->Subject = $asunto;
        $mail->isHTML(true);
        $mail->Body = $html;
        $mail->AltBody = $texto;
        mm_agregar_adjuntos_mailer($mail, $adjuntos);
        if ($rutaPdf !== '' && is_file($rutaPdf)) {
            $mail->addAttachment($rutaPdf);
        }
        return $mail->send();
    } catch (Throwable $e) {
        Logger::error('No se pudo enviar correo de entrada', $e, ['to' => $destinatarioEmail]);
        return false;
    }
}

function enviarCorreoRecuperacion(string $destinatarioEmail, string $destinatarioNombre, string $token): bool
{
    $url = mm_url_absoluta('pages/restablecer_password.php?token=' . urlencode($token));
    $asunto = 'Restablece tu contrasena de MMCinema';
    $html = mm_html_base($asunto, '
        <h1 style="margin:0 0 12px;font-size:24px;color:#f9fafb;">Restablecer contrasena</h1>
        <p style="color:#d1d5db;line-height:1.6;">Hola ' . htmlspecialchars($destinatarioNombre, ENT_QUOTES, 'UTF-8') . ', usa este enlace para crear una nueva contrasena.</p>
        <p style="text-align:center;margin:28px 0;">
            <a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;background:#f59e0b;color:#111827;text-decoration:none;font-weight:bold;padding:13px 18px;border-radius:10px;">Cambiar contrasena</a>
        </p>
        <p style="color:#9ca3af;font-size:13px;line-height:1.5;">El enlace caduca en 1 hora. Si el boton no funciona, abre este enlace:<br>' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</p>
    ');
    $texto = "Hola $destinatarioNombre,\n\nRestablece tu contrasena aqui:\n$url\n\nEl enlace caduca en 1 hora.";

    $adjuntos = [];

    if (MM_SENDGRID_API_KEY !== '') {
        return mm_enviar_con_sendgrid($destinatarioEmail, $destinatarioNombre, $asunto, $html, $texto, $adjuntos);
    }

    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);
        $mail->Subject = $asunto;
        $mail->isHTML(true);
        $mail->Body = $html;
        $mail->AltBody = $texto;
        mm_agregar_adjuntos_mailer($mail, $adjuntos);
        return $mail->send();
    } catch (Throwable $e) {
        Logger::error('No se pudo enviar correo de recuperacion', $e, ['to' => $destinatarioEmail]);
        return false;
    }
}
