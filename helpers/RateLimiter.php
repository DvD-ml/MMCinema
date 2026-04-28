<?php
/**
 * Sistema de Rate Limiting para prevenir ataques de fuerza bruta
 */

class RateLimiter {
    const MAX_INTENTOS = 5;
    const TIEMPO_BLOQUEO = 900; // 15 minutos en segundos
    
    /**
     * Registrar intento fallido de login
     */
    public static function registrarIntentoFallido($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_intentos_' . md5($email);
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        $_SESSION[$key . '_timestamp'] = time();
    }
    
    /**
     * Verificar si el usuario está bloqueado
     */
    public static function estaBloqueado($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_intentos_' . md5($email);
        $intentos = $_SESSION[$key] ?? 0;
        $timestamp = $_SESSION[$key . '_timestamp'] ?? 0;
        
        if ($intentos >= self::MAX_INTENTOS) {
            $tiempoTranscurrido = time() - $timestamp;
            if ($tiempoTranscurrido < self::TIEMPO_BLOQUEO) {
                return true;
            } else {
                // Desbloquear después del tiempo
                unset($_SESSION[$key]);
                unset($_SESSION[$key . '_timestamp']);
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Obtener tiempo restante de bloqueo en segundos
     */
    public static function getTiempoRestante($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_intentos_' . md5($email);
        $timestamp = $_SESSION[$key . '_timestamp'] ?? 0;
        
        if ($timestamp === 0) {
            return 0;
        }
        
        $tiempoTranscurrido = time() - $timestamp;
        $tiempoRestante = self::TIEMPO_BLOQUEO - $tiempoTranscurrido;
        
        return max(0, $tiempoRestante);
    }
    
    /**
     * Limpiar intentos fallidos después de login exitoso
     */
    public static function limpiarIntentos($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'login_intentos_' . md5($email);
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_timestamp']);
    }
}
?>
