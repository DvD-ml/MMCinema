<?php

class Validator {
    
    public static function validarId($valor, $minimo = 1) {
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        
        if ($id === false) {
            return null;
        }
        
        if ($id >= $minimo) {
            return $id;
        } else {
            return null;
        }
    }

    public static function validarEmail($email) {
        $emailLimpio = filter_var($email, FILTER_SANITIZE_EMAIL);
        $esValido = filter_var($emailLimpio, FILTER_VALIDATE_EMAIL);
        
        if ($esValido) {
            return $emailLimpio;
        } else {
            return null;
        }
    }

    public static function sanitizarTexto($texto) {
        $textoTrimmed = trim($texto);
        $textoSanitizado = htmlspecialchars($textoTrimmed, ENT_QUOTES, 'UTF-8');
        return $textoSanitizado;
    }

    public static function validarFecha($fecha) {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        
        if ($d === false) {
            return null;
        }
        
        $fechaFormateada = $d->format('Y-m-d');
        
        if ($fechaFormateada === $fecha) {
            return $fecha;
        } else {
            return null;
        }
    }

    public static function validarPuntuacion($puntuacion) {
        $punt = filter_var($puntuacion, FILTER_VALIDATE_INT);
        
        if ($punt === false) {
            return null;
        }
        
        if ($punt >= 1 && $punt <= 5) {
            return $punt;
        } else {
            return null;
        }
    }

    public static function validarUrl($url) {
        $esValida = filter_var($url, FILTER_VALIDATE_URL);
        
        if ($esValida) {
            return $url;
        } else {
            return null;
        }
    }

    public static function validarDecimalPositivo($numero) {
        $num = filter_var($numero, FILTER_VALIDATE_FLOAT);
        
        if ($num === false) {
            return null;
        }
        
        if ($num > 0) {
            return $num;
        } else {
            return null;
        }
    }

    public static function validarLongitud($texto, $min, $max) {
        $longitud = mb_strlen($texto);
        
        if ($longitud >= $min && $longitud <= $max) {
            return true;
        } else {
            return false;
        }
    }
}
?>