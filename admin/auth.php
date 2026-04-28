<?php
/**
 * Sistema de autenticación para el panel de administración
 */

function verificarAuth() {
    // Verificar si hay sesión iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    // Verificar si el usuario es administrador
    if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
        header('Location: ../index.php');
        exit;
    }
    
    return true;
}

function esAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['es_admin']) && $_SESSION['es_admin'] == 1;
}

function obtenerUsuarioActual() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'es_admin' => $_SESSION['es_admin'] ?? 0
    ];
}

function cerrarSesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    session_destroy();
    header('Location: ../login.php');
    exit;
}
?>