<?php

/**
 * Clase de validación centralizada para inputs
 */
class Validator
{
    /**
     * Valida un ID numérico
     * 
     * @param mixed $valor Valor a validar
     * @param int $minimo Valor mínimo permitido
     * @return int|null ID validado o null si es inválido
     */
    public static function validarId($valor, int $minimo = 1): ?int
    {
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return ($id !== false && $id >= $minimo) ? $id : null;
    }

    /**
     * Valida un email
     * 
     * @param string $email Email a validar
     * @return string|null Email validado o null si es inválido
     */
    public static function validarEmail(string $email): ?string
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /**
     * Sanitiza una cadena de texto
     * 
     * @param string $texto Texto a sanitizar
     * @return string Texto sanitizado
     */
    public static function sanitizarTexto(string $texto): string
    {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida una fecha en formato Y-m-d
     * 
     * @param string $fecha Fecha a validar
     * @return string|null Fecha validada o null si es inválida
     */
    public static function validarFecha(string $fecha): ?string
    {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return ($d && $d->format('Y-m-d') === $fecha) ? $fecha : null;
    }

    /**
     * Valida una puntuación (1-5)
     * 
     * @param mixed $puntuacion Puntuación a validar
     * @return int|null Puntuación validada o null si es inválida
     */
    public static function validarPuntuacion($puntuacion): ?int
    {
        $punt = filter_var($puntuacion, FILTER_VALIDATE_INT);
        return ($punt !== false && $punt >= 1 && $punt <= 5) ? $punt : null;
    }

    /**
     * Valida una URL
     * 
     * @param string $url URL a validar
     * @return string|null URL validada o null si es inválida
     */
    public static function validarUrl(string $url): ?string
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * Valida un número decimal positivo
     * 
     * @param mixed $numero Número a validar
     * @return float|null Número validado o null si es inválido
     */
    public static function validarDecimalPositivo($numero): ?float
    {
        $num = filter_var($numero, FILTER_VALIDATE_FLOAT);
        return ($num !== false && $num > 0) ? $num : null;
    }

    /**
     * Valida longitud de texto
     * 
     * @param string $texto Texto a validar
     * @param int $min Longitud mínima
     * @param int $max Longitud máxima
     * @return bool True si es válido
     */
    public static function validarLongitud(string $texto, int $min, int $max): bool
    {
        $longitud = mb_strlen($texto);
        return $longitud >= $min && $longitud <= $max;
    }
}
