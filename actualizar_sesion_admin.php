<?php
/**
 * Script para actualizar la sesión del usuario admin
 * Ejecutar una vez para corregir el problema de acceso al panel
 */

session_start();
require_once "config/conexion.php";

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Actualizar Sesión Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: #0f172a;
            color: #fff;
            text-align: center;
        }
        h1 { color: #f97316; margin-bottom: 30px; }
        .box {
            background: rgba(255,255,255,0.05);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }
        .success { color: #10b981; font-size: 3rem; }
        .error { color: #ef4444; font-size: 3rem; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px;
            font-weight: bold;
        }
        .btn:hover { background: #ea580c; }
    </style>
</head>
<body>";

echo "<h1>🔄 Actualizar Sesión Admin</h1>";

if (!isset($_SESSION['usuario_id'])) {
    echo "<div class='box'>";
    echo "<div class='error'>❌</div>";
    echo "<h2>No hay sesión iniciada</h2>";
    echo "<p>Debes iniciar sesión primero.</p>";
    echo "<a href='login.php' class='btn'>🔐 Iniciar Sesión</a>";
    echo "</div>";
} else {
    try {
        $userId = $_SESSION['usuario_id'];
        
        // Obtener datos actualizados del usuario
        $stmt = $pdo->prepare("SELECT id, username, email, es_admin FROM usuario WHERE id = ?");
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            // Actualizar sesión
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            
            echo "<div class='box'>";
            echo "<div class='success'>✅</div>";
            echo "<h2>Sesión Actualizada</h2>";
            echo "<p><strong>Usuario:</strong> " . ($usuario['username'] ?? $usuario['email']) . "</p>";
            echo "<p><strong>Email:</strong> {$usuario['email']}</p>";
            echo "<p><strong>Es Admin:</strong> " . ($usuario['es_admin'] ? 'SÍ ✅' : 'NO ❌') . "</p>";
            
            if ($usuario['es_admin'] == 1) {
                echo "<p style='color: #10b981; margin-top: 20px;'>¡Ahora puedes acceder al panel de administración!</p>";
                echo "<a href='admin/carrusel_destacado.php' class='btn'>🎬 Ir al Panel Carrusel</a>";
                echo "<a href='admin/index.php' class='btn'>📊 Panel Admin Principal</a>";
            } else {
                echo "<p style='color: #ef4444; margin-top: 20px;'>Este usuario no tiene permisos de administrador.</p>";
                echo "<p>Contacta al administrador del sistema.</p>";
            }
            
            echo "</div>";
        } else {
            echo "<div class='box'>";
            echo "<div class='error'>❌</div>";
            echo "<h2>Usuario no encontrado</h2>";
            echo "<p>No se encontró el usuario en la base de datos.</p>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='box'>";
        echo "<div class='error'>❌</div>";
        echo "<h2>Error</h2>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

echo "<div style='margin-top: 30px;'>";
echo "<a href='index.php' class='btn' style='background: #6b7280;'>🏠 Volver al Inicio</a>";
echo "</div>";

echo "</body></html>";
?>
