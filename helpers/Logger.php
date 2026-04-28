<?php

/**
 * Clase de logging centralizado
 */
class Logger
{
    private static string $logDir = __DIR__ . '/../logs';
    private static string $logFile = 'app.log';

    /**
     * Inicializa el directorio de logs
     */
    private static function init(): void
    {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    /**
     * Escribe un mensaje en el log
     * 
     * @param string $nivel Nivel del log (INFO, WARNING, ERROR, etc.)
     * @param string $mensaje Mensaje a registrar
     * @param array $contexto Contexto adicional
     */
    private static function escribir(string $nivel, string $mensaje, array $contexto = []): void
    {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $usuario = $_SESSION['usuario_id'] ?? 'guest';
        
        $linea = sprintf(
            "[%s] [%s] [IP: %s] [User: %s] %s",
            $timestamp,
            $nivel,
            $ip,
            $usuario,
            $mensaje
        );

        if (!empty($contexto)) {
            $linea .= ' | Context: ' . json_encode($contexto, JSON_UNESCAPED_UNICODE);
        }

        $linea .= PHP_EOL;

        $archivo = self::$logDir . '/' . self::$logFile;
        file_put_contents($archivo, $linea, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log de información
     */
    public static function info(string $mensaje, array $contexto = []): void
    {
        self::escribir('INFO', $mensaje, $contexto);
    }

    /**
     * Log de advertencia
     */
    public static function warning(string $mensaje, array $contexto = []): void
    {
        self::escribir('WARNING', $mensaje, $contexto);
    }

    /**
     * Log de error
     */
    public static function error(string $mensaje, ?\Throwable $exception = null, array $contexto = []): void
    {
        if ($exception) {
            $contexto['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        }

        self::escribir('ERROR', $mensaje, $contexto);
    }

    /**
     * Log de debug (solo en desarrollo)
     */
    public static function debug(string $mensaje, array $contexto = []): void
    {
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            self::escribir('DEBUG', $mensaje, $contexto);
        }
    }

    /**
     * Log de actividad de seguridad
     */
    public static function security(string $mensaje, array $contexto = []): void
    {
        self::escribir('SECURITY', $mensaje, $contexto);
    }
}
