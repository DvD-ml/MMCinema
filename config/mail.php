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
    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);

        [$rutaLogo, $logoDisponible] = mm_obtener_logo_correo();

        if ($logoDisponible) {
            $mail->addEmbeddedImage($rutaLogo, 'logo_mmcinema');
        }

        $url = MM_BASE_URL . '/verificar.php?token=' . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Verifica tu cuenta en MMCinema';

        $logoHtml = $logoDisponible
            ? "<img src='cid:logo_mmcinema' alt='MMCinema' style='max-width:180px; height:auto;'>"
            : "<h1 style='margin:0; color:#ffffff; font-size:28px;'>MMCinema</h1>";

        $mail->Body = "
        <div style='margin:0; padding:30px; background:#f4f4f4; font-family:Arial, Helvetica, sans-serif;'>
            <div style='max-width:640px; margin:0 auto; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.10);'>
                
                <div style='background:linear-gradient(135deg, #111827 0%, #1f2937 100%); padding:30px; text-align:center;'>
                    {$logoHtml}
                </div>

                <div style='padding:40px 35px; color:#1f2937;'>
                    <h2 style='margin-top:0; font-size:30px; color:#111827;'>Hola, {$destinatarioNombre}</h2>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Gracias por registrarte en <strong>MMCinema</strong>.
                    </p>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Para activar tu cuenta, pulsa en el siguiente botón:
                    </p>

                    <div style='text-align:center; margin:35px 0 25px 0;'>
                        <a href='{$url}'
                           style='display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:bold; font-size:16px;'>
                           Verificar cuenta
                        </a>
                    </div>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563;'>
                        Si el botón no funciona, copia y pega este enlace en tu navegador:
                    </p>

                    <p style='font-size:14px; line-height:1.6; color:#111827; word-break:break-all;'>
                        {$url}
                    </p>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563; margin-bottom:0;'>
                        Este enlace caduca en 24 horas.<br>
                        <strong>El equipo de MMCinema</strong>
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->AltBody =
            "Hola {$destinatarioNombre}. Verifica tu cuenta en MMCinema entrando en este enlace: {$url}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function enviarCorreoBienvenida(string $destinatarioEmail, string $destinatarioNombre): bool
{
    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);

        [$rutaLogo, $logoDisponible] = mm_obtener_logo_correo();

        if ($logoDisponible) {
            $mail->addEmbeddedImage($rutaLogo, 'logo_mmcinema');
        }

        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a MMCinema!';

        $logoHtml = $logoDisponible
            ? "<img src='cid:logo_mmcinema' alt='MMCinema' style='max-width:180px; height:auto;'>"
            : "<h1 style='margin:0; color:#ffffff; font-size:28px;'>MMCinema</h1>";

        $mail->Body = "
        <div style='margin:0; padding:30px; background:#f4f4f4; font-family:Arial, Helvetica, sans-serif;'>
            <div style='max-width:640px; margin:0 auto; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.10);'>
                
                <div style='background:linear-gradient(135deg, #111827 0%, #1f2937 100%); padding:30px; text-align:center;'>
                    {$logoHtml}
                </div>

                <div style='padding:40px 35px; color:#1f2937;'>
                    <h2 style='margin-top:0; font-size:30px; color:#111827;'>¡Bienvenido a MMCinema, {$destinatarioNombre}!</h2>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Tu cuenta ya está verificada y lista para usarse.
                    </p>

                    <div style='background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; margin:25px 0;'>
                        <p style='margin:0 0 8px 0; font-size:15px;'><strong>Usuario:</strong> {$destinatarioNombre}</p>
                        <p style='margin:0; font-size:15px;'><strong>Correo:</strong> {$destinatarioEmail}</p>
                    </div>

                    <div style='text-align:center; margin:35px 0 25px 0;'>
                        <a href='" . MM_BASE_URL . "/login.php'
                           style='display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:bold; font-size:16px;'>
                           Iniciar sesión
                        </a>
                    </div>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563; margin-bottom:0;'>
                        Gracias por confiar en nosotros.<br>
                        <strong>El equipo de MMCinema</strong>
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->AltBody =
            "Bienvenido a MMCinema, {$destinatarioNombre}. Tu cuenta ya está verificada.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function enviarCorreoEntrada(
    string $destinatarioEmail,
    string $destinatarioNombre,
    array $ticket,
    string $rutaPdf
): bool {
    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);

        [$rutaLogo, $logoDisponible] = mm_obtener_logo_correo();

        if ($logoDisponible) {
            $mail->addEmbeddedImage($rutaLogo, 'logo_mmcinema');
        }

        if (file_exists($rutaPdf)) {
            $mail->addAttachment($rutaPdf, 'entrada-mmcinema-' . $ticket['codigo'] . '.pdf');
        }

        $mail->isHTML(true);
        $mail->Subject = 'Tu entrada de MMCinema - ' . $ticket['titulo'];

        $logoHtml = $logoDisponible
            ? "<img src='cid:logo_mmcinema' alt='MMCinema' style='max-width:180px; height:auto;'>"
            : "<h1 style='margin:0; color:#ffffff; font-size:28px;'>MMCinema</h1>";

        $mail->Body = "
        <div style='margin:0; padding:30px; background:#f4f4f4; font-family:Arial, Helvetica, sans-serif;'>
            <div style='max-width:680px; margin:0 auto; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.10);'>
                
                <div style='background:linear-gradient(135deg, #111827 0%, #1f2937 100%); padding:30px; text-align:center;'>
                    {$logoHtml}
                </div>

                <div style='padding:40px 35px; color:#1f2937;'>
                    <h2 style='margin-top:0; font-size:30px; color:#111827;'>Hola, {$destinatarioNombre}</h2>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Tu reserva se ha realizado correctamente en <strong>MMCinema</strong>.
                    </p>

                    <div style='background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; margin:25px 0;'>
                        <p style='margin:0 0 10px 0;'><strong>Película:</strong> {$ticket['titulo']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Fecha:</strong> {$ticket['fecha']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Hora:</strong> {$ticket['hora']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Sala:</strong> {$ticket['sala']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Entradas:</strong> {$ticket['cantidad']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Asientos:</strong> {$ticket['asientos']}</p>
                        <p style='margin:0 0 10px 0;'><strong>Total:</strong> {$ticket['total']} €</p>
                        <p style='margin:0;'><strong>Código:</strong> {$ticket['codigo']}</p>
                    </div>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        En este correo te adjuntamos el <strong>PDF de tu entrada</strong>.
                    </p>

                    <div style='text-align:center; margin:35px 0 25px 0;'>
                        <a href='" . MM_BASE_URL . "/perfil.php'
                           style='display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:bold; font-size:16px;'>
                           Ver mi perfil
                        </a>
                    </div>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563; margin-bottom:0;'>
                        Gracias por confiar en MMCinema.<br>
                        <strong>¡Te esperamos en sala!</strong>
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->AltBody =
            "Reserva confirmada en MMCinema. Película: {$ticket['titulo']}. Fecha: {$ticket['fecha']} {$ticket['hora']}. Código: {$ticket['codigo']}.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function enviarCorreoRecuperacion(string $destinatarioEmail, string $destinatarioNombre, string $token): bool
{
    try {
        $mail = mm_configurar_mailer();
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);

        [$rutaLogo, $logoDisponible] = mm_obtener_logo_correo();

        if ($logoDisponible) {
            $mail->addEmbeddedImage($rutaLogo, 'logo_mmcinema');
        }

        $url = MM_BASE_URL . '/restablecer_password.php?token=' . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Recupera tu contraseúa en MMCinema';

        $logoHtml = $logoDisponible
            ? "<img src='cid:logo_mmcinema' alt='MMCinema' style='max-width:180px; height:auto;'>"
            : "<h1 style='margin:0; color:#ffffff; font-size:28px;'>MMCinema</h1>";

        $mail->Body = "
        <div style='margin:0; padding:30px; background:#f4f4f4; font-family:Arial, Helvetica, sans-serif;'>
            <div style='max-width:640px; margin:0 auto; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.10);'>
                
                <div style='background:linear-gradient(135deg, #111827 0%, #1f2937 100%); padding:30px; text-align:center;'>
                    {$logoHtml}
                </div>

                <div style='padding:40px 35px; color:#1f2937;'>
                    <h2 style='margin-top:0; font-size:30px; color:#111827;'>Hola, {$destinatarioNombre}</h2>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Hemos recibido una solicitud para restablecer tu contraseúa en <strong>MMCinema</strong>.
                    </p>

                    <p style='font-size:16px; line-height:1.7; margin-bottom:18px;'>
                        Pulsa en el siguiente botón para crear una nueva contraseúa:
                    </p>

                    <div style='text-align:center; margin:35px 0 25px 0;'>
                        <a href='{$url}'
                           style='display:inline-block; background:#f59e0b; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:10px; font-weight:bold; font-size:16px;'>
                           Restablecer contraseúa
                        </a>
                    </div>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563;'>
                        Si no has solicitado este cambio, puedes ignorar este correo.
                    </p>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563;'>
                        Si el botón no funciona, copia y pega este enlace en tu navegador:
                    </p>

                    <p style='font-size:14px; line-height:1.6; color:#111827; word-break:break-all;'>
                        {$url}
                    </p>

                    <p style='font-size:15px; line-height:1.7; color:#4b5563; margin-bottom:0;'>
                        Este enlace caduca en 1 hora.<br>
                        <strong>El equipo de MMCinema</strong>
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->AltBody =
            "Hola {$destinatarioNombre}. Para restablecer tu contraseúa entra aquí: {$url}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}