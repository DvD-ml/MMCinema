<?php

/**
 * Clase para protección CSRF (Cross-Site Request Forgery)
 */
class CSRF
{
    /**
     * Genera un token CSRF y lo guarda en sesión
     * 
     * @return string Token generado
     */
    public static function generarToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un token CSRF
     * 
     * @param string|null $token Token a validar
     * @return bool True si es válido
     */
    public static function validarToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Genera el campo HTML del token CSRF
     * 
     * @return string HTML del input hidden
     */
    public static function campoFormulario(): string
    {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida el token desde POST y termina ejecución si es inválido
     * 
     * @param string $mensajeError Mensaje de error personalizado
     * @return void
     */
    public static function validarOAbortar(string $mensajeError = 'Token CSRF inválido'): void
    {
        $token = $_POST['csrf_token'] ?? null;
        
        if (!self::validarToken($token)) {
            http_response_code(403);
            die($mensajeError);
        }
    }

    /**
     * Regenera el token CSRF (útil después de login/logout)
     * 
     * @return string Nuevo token
     */
    public static function regenerarToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}
