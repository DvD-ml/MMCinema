<?php

/**
 * Clase de autenticación con soporte para "Recordar sesión"
 */
class Auth
{
    /**
     * Verifica si hay una sesión activa o una cookie de recordar
     * 
     * @param PDO $pdo Conexión a la base de datos
     * @return bool True si el usuario está autenticado
     */
    public static function verificarSesion(PDO $pdo): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si ya hay sesión activa, está autenticado
        if (!empty($_SESSION['usuario_id'])) {
            return true;
        }

        // Si no hay sesión, verificar cookie de "recordar"
        if (isset($_COOKIE['remember_token'])) {
            return self::restaurarSesionDesdeCookie($pdo);
        }

        return false;
    }

    /**
     * Restaura la sesión desde una cookie de "recordar"
     * 
     * @param PDO $pdo Conexión a la base de datos
     * @return bool True si se restauró la sesión
     */
    private static function restaurarSesionDesdeCookie(PDO $pdo): bool
    {
        $token = $_COOKIE['remember_token'];

        // Buscar usuario con ese token válido
        $stm = $pdo->prepare("
            SELECT id, username, email, rol
            FROM usuario
            WHERE remember_token = ?
              AND remember_expira > NOW()
              AND verificado = 1
            LIMIT 1
        ");
        $stm->execute([$token]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Token inválido o expirado, eliminar cookie
            self::eliminarCookieRecordar();
            Logger::warning("Intento de restaurar sesión con token inválido");
            return false;
        }

        // Restaurar sesión
        $_SESSION['usuario_id'] = (int)$user['id'];
        $_SESSION['usuario']    = $user['username'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['rol']        = $user['rol'];

        Logger::info("Sesión restaurada desde cookie", ['user_id' => $user['id']]);

        return true;
    }

    /**
     * Cierra la sesión y elimina la cookie de recordar
     * 
     * @param PDO $pdo Conexión a la base de datos
     */
    public static function cerrarSesion(PDO $pdo): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario_id = $_SESSION['usuario_id'] ?? null;

        // Eliminar token de BD
        if ($usuario_id) {
            $stm = $pdo->prepare("
                UPDATE usuario 
                SET remember_token = NULL, remember_expira = NULL
                WHERE id = ?
            ");
            $stm->execute([$usuario_id]);

            Logger::info("Usuario cerró sesión", ['user_id' => $usuario_id]);
        }

        // Eliminar cookie
        self::eliminarCookieRecordar();

        // Destruir sesión
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Elimina la cookie de recordar sesión
     */
    private static function eliminarCookieRecordar(): void
    {
        if (isset($_COOKIE['remember_token'])) {
            setcookie(
                'remember_token',
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * 
     * @param string $rol Rol requerido (ej: 'admin')
     * @return bool True si el usuario tiene ese rol
     */
    public static function tieneRol(string $rol): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['rol']) && $_SESSION['rol'] === $rol;
    }

    /**
     * Requiere autenticación, redirige a login si no está autenticado
     * 
     * @param PDO $pdo Conexión a la base de datos
     * @param string $redirect URL de redirección después del login
     */
    public static function requerirLogin(PDO $pdo, string $redirect = ''): void
    {
        if (!self::verificarSesion($pdo)) {
            $url = 'login.php';
            if ($redirect) {
                $url .= '?redirect=' . urlencode($redirect);
            }
            header("Location: $url");
            exit();
        }
    }
}
