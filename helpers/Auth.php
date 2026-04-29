<?php

class Auth {
    
    public static function verificarSesion($pdo) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['usuario_id'])) {
            return true;
        }

        if (isset($_COOKIE['remember_token'])) {
            $resultado = self::restaurarSesionDesdeCookie($pdo);
            return $resultado;
        }

        return false;
    }

    private static function restaurarSesionDesdeCookie($pdo) {
        $token = $_COOKIE['remember_token'];

        $sql = "SELECT id, username, email, rol FROM usuario WHERE remember_token = ? AND remember_expira > NOW() AND verificado = 1 LIMIT 1";
        $stm = $pdo->prepare($sql);
        $stm->execute([$token]);
        $user = $stm->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            self::eliminarCookieRecordar();
            return false;
        }

        $_SESSION['usuario_id'] = (int)$user['id'];
        $_SESSION['usuario'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['rol'] = $user['rol'];

        return true;
    }

    public static function cerrarSesion($pdo) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario_id = null;
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
        }

        if ($usuario_id) {
            $sql = "UPDATE usuario SET remember_token = NULL, remember_expira = NULL WHERE id = ?";
            $stm = $pdo->prepare($sql);
            $stm->execute([$usuario_id]);
        }

        self::eliminarCookieRecordar();

        $_SESSION = [];
        session_destroy();
    }

    private static function eliminarCookieRecordar() {
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

    public static function tieneRol($rol) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['rol'])) {
            if ($_SESSION['rol'] === $rol) {
                return true;
            }
        }

        return false;
    }

    public static function requerirLogin($pdo, $redirect = '') {
        $estaAutenticado = self::verificarSesion($pdo);
        
        if (!$estaAutenticado) {
            $url = 'login.php';
            if ($redirect !== '') {
                $url = $url . '?redirect=' . urlencode($redirect);
            }
            header("Location: $url");
            exit();
        }
    }
}
?>
