<?php

class Logger {
    private static $logDir = null;
    private static $logFile = 'app.log';

    private static function init() {
        if (self::$logDir === null) {
            self::$logDir = __DIR__ . '/../logs';
        }
        
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    private static function escribir($nivel, $mensaje, $contexto = []) {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $ip = 'CLI';
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $usuario = 'guest';
        if (isset($_SESSION['usuario_id'])) {
            $usuario = $_SESSION['usuario_id'];
        }
        
        $linea = "[" . $timestamp . "] [" . $nivel . "] [IP: " . $ip . "] [User: " . $usuario . "] " . $mensaje;

        if (!empty($contexto)) {
            $contextoJson = json_encode($contexto, JSON_UNESCAPED_UNICODE);
            $linea = $linea . " | Context: " . $contextoJson;
        }

        $linea = $linea . PHP_EOL;

        $archivo = self::$logDir . '/' . self::$logFile;
        file_put_contents($archivo, $linea, FILE_APPEND | LOCK_EX);
    }

    public static function info($mensaje, $contexto = []) {
        self::escribir('INFO', $mensaje, $contexto);
    }

    public static function warning($mensaje, $contexto = []) {
        self::escribir('WARNING', $mensaje, $contexto);
    }

    public static function error($mensaje, $exception = null, $contexto = []) {
        if ($exception !== null) {
            $mensaje_exception = $exception->getMessage();
            $file_exception = $exception->getFile();
            $line_exception = $exception->getLine();
            $trace_exception = $exception->getTraceAsString();
            
            $contexto['exception'] = [
                'message' => $mensaje_exception,
                'file' => $file_exception,
                'line' => $line_exception,
                'trace' => $trace_exception
            ];
        }

        self::escribir('ERROR', $mensaje, $contexto);
    }

    public static function debug($mensaje, $contexto = []) {
        $env = 'production';
        if (isset($_ENV['APP_ENV'])) {
            $env = $_ENV['APP_ENV'];
        }
        
        if ($env === 'development') {
            self::escribir('DEBUG', $mensaje, $contexto);
        }
    }

    public static function security($mensaje, $contexto = []) {
        self::escribir('SECURITY', $mensaje, $contexto);
    }
}
?>
