<?php
/**
 * Script para procesar cola de emails con SendGrid API
 * Ejecutar cada minuto con cron: * * * * * php /var/www/html/mmcinema/scripts/procesar_emails.php
 */

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/mail.php';

try {
    // Obtener emails no enviados (máximo 10 por ejecución)
    $sql = "SELECT * FROM email_queue WHERE enviado = 0 AND intentos < 3 ORDER BY creado ASC LIMIT 10";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    $emails = $stm->fetchAll();

    foreach ($emails as $email) {
        try {
            $enviado = false;
            
            switch ($email['tipo']) {
                case 'verificacion':
                    $enviado = enviarCorreoVerificacion(
                        $email['destinatario_email'], 
                        $email['destinatario_nombre'], 
                        $email['token']
                    );
                    break;
                case 'bienvenida':
                    $enviado = enviarCorreoBienvenida(
                        $email['destinatario_email'], 
                        $email['destinatario_nombre']
                    );
                    break;
                case 'recuperacion':
                    $enviado = enviarCorreoRecuperacion(
                        $email['destinatario_email'], 
                        $email['destinatario_nombre'], 
                        $email['token']
                    );
                    break;
            }
            
            if ($enviado) {
                // Marcar como enviado
                $sql = "UPDATE email_queue SET enviado = 1 WHERE id = ?";
                $stm = $pdo->prepare($sql);
                $stm->execute([$email['id']]);
            } else {
                // Incrementar intentos
                $sql = "UPDATE email_queue SET intentos = intentos + 1 WHERE id = ?";
                $stm = $pdo->prepare($sql);
                $stm->execute([$email['id']]);
            }
        } catch (Exception $e) {
            // Incrementar intentos en caso de error
            $sql = "UPDATE email_queue SET intentos = intentos + 1 WHERE id = ?";
            $stm = $pdo->prepare($sql);
            $stm->execute([$email['id']]);
        }
    }
} catch (Exception $e) {
    // Log silencioso
}

exit(0);
?>
