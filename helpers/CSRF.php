<?php

class CSRF {
    
    public static function generarToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $bytesAleatorios = random_bytes(32);
            $tokenHex = bin2hex($bytesAleatorios);
            $_SESSION['csrf_token'] = $tokenHex;
        }

        return $_SESSION['csrf_token'];
    }

    public static function validarToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        if (empty($token)) {
            return false;
        }

        $tokenSesion = $_SESSION['csrf_token'];
        $esValido = hash_equals($tokenSesion, $token);
        
        return $esValido;
    }

    public static function campoFormulario() {
        $token = self::generarToken();
        $tokenEscapado = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
        $html = '<input type="hidden" name="csrf_token" value="' . $tokenEscapado . '">';
        return $html;
    }

    public static function validarOAbortar($mensajeError = 'Token CSRF inválido') {
        $token = null;
        
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        }
        
        $esValido = self::validarToken($token);
        
        if (!$esValido) {
            http_response_code(403);
            die($mensajeError);
        }
    }

    public static function regenerarToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $bytesAleatorios = random_bytes(32);
        $tokenHex = bin2hex($bytesAleatorios);
        $_SESSION['csrf_token'] = $tokenHex;
        
        return $_SESSION['csrf_token'];
    }
}
?>
