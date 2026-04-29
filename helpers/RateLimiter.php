<?php

class RateLimiter {
    const MAX_INTENTOS = 5;
    const TIEMPO_BLOQUEO = 900;
    
    public static function registrarIntentoFallido($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $emailHash = md5($email);
        $keyIntentos = 'login_intentos_' . $emailHash;
        $keyTimestamp = 'login_intentos_' . $emailHash . '_timestamp';
        
        if (isset($_SESSION[$keyIntentos])) {
            $_SESSION[$keyIntentos] = $_SESSION[$keyIntentos] + 1;
        } else {
            $_SESSION[$keyIntentos] = 1;
        }
        
        $_SESSION[$keyTimestamp] = time();
    }
    
    public static function estaBloqueado($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $emailHash = md5($email);
        $keyIntentos = 'login_intentos_' . $emailHash;
        $keyTimestamp = 'login_intentos_' . $emailHash . '_timestamp';
        
        $intentos = 0;
        $timestamp = 0;
        
        if (isset($_SESSION[$keyIntentos])) {
            $intentos = $_SESSION[$keyIntentos];
        }
        
        if (isset($_SESSION[$keyTimestamp])) {
            $timestamp = $_SESSION[$keyTimestamp];
        }
        
        if ($intentos >= self::MAX_INTENTOS) {
            $ahora = time();
            $tiempoTranscurrido = $ahora - $timestamp;
            
            if ($tiempoTranscurrido < self::TIEMPO_BLOQUEO) {
                return true;
            } else {
                unset($_SESSION[$keyIntentos]);
                unset($_SESSION[$keyTimestamp]);
                return false;
            }
        }
        
        return false;
    }
    
    public static function getTiempoRestante($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $emailHash = md5($email);
        $keyTimestamp = 'login_intentos_' . $emailHash . '_timestamp';
        
        $timestamp = 0;
        if (isset($_SESSION[$keyTimestamp])) {
            $timestamp = $_SESSION[$keyTimestamp];
        }
        
        if ($timestamp === 0) {
            return 0;
        }
        
        $ahora = time();
        $tiempoTranscurrido = $ahora - $timestamp;
        $tiempoRestante = self::TIEMPO_BLOQUEO - $tiempoTranscurrido;
        
        if ($tiempoRestante < 0) {
            $tiempoRestante = 0;
        }
        
        return $tiempoRestante;
    }
    
    public static function limpiarIntentos($email) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $emailHash = md5($email);
        $keyIntentos = 'login_intentos_' . $emailHash;
        $keyTimestamp = 'login_intentos_' . $emailHash . '_timestamp';
        
        if (isset($_SESSION[$keyIntentos])) {
            unset($_SESSION[$keyIntentos]);
        }
        
        if (isset($_SESSION[$keyTimestamp])) {
            unset($_SESSION[$keyTimestamp]);
        }
    }
}
?>
