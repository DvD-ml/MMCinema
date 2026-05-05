<?php
/**
 * Sistema de autenticacion para el panel de administracion.
 *
 * Las URLs se calculan desde SCRIPT_NAME para que el proyecto funcione tanto
 * en la raiz del dominio como dentro de subcarpetas (/mmcinema, /david/MMCINEMA).
 */

if (!function_exists('mm_base_path')) {
    function mm_base_path(): string {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $adminPos = strpos($scriptName, '/admin/');

        if ($adminPos !== false) {
            return rtrim(substr($scriptName, 0, $adminPos), '/');
        }

        $pagesPos = strpos($scriptName, '/pages/');
        if ($pagesPos !== false) {
            return rtrim(substr($scriptName, 0, $pagesPos), '/');
        }

        return '';
    }
}

if (!function_exists('mm_url')) {
    function mm_url(string $path = ''): string {
        $base = mm_base_path();
        $path = '/' . ltrim($path, '/');

        if ($path === '/') {
            return $base !== '' ? $base . '/' : '/';
        }

        return $base . $path;
    }
}

if (!function_exists('mm_admin_url')) {
    function mm_admin_url(string $path = ''): string {
        return mm_url('admin/pages/' . ltrim($path, '/'));
    }
}

if (!function_exists('mm_asset_url')) {
    function mm_asset_url(?string $path): string {
        if ($path === null || trim($path) === '') {
            return '';
        }

        return mm_url(ltrim($path, '/'));
    }
}

function verificarAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
        header('Location: ' . mm_url('pages/login.php') . '?redirect=' . $redirect);
        exit;
    }

    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header('Location: ' . mm_url('pages/index.php'));
        exit;
    }

    return true;
}

function esAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function obtenerUsuarioActual() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'username' => $_SESSION['usuario'] ?? ($_SESSION['username'] ?? null),
        'email' => $_SESSION['email'] ?? null,
        'rol' => $_SESSION['rol'] ?? null,
        'es_admin' => (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') ? 1 : 0
    ];
}

function cerrarSesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_destroy();
    header('Location: ' . mm_url('pages/login.php'));
    exit;
}
?>
