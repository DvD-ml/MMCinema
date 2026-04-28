<?php
require_once "auth.php";
verificarAuth();

session_start();
require_once "../config/conexion.php";

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Debug Sesión Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #0f172a;
            color: #fff;
        }
        h1 { color: #f97316; }
        .info { 
            background: rgba(255,255,255,0.05); 
            padding: 15px; 
            border-radius: 8px; 
            margin: 10px 0;
            border-left: 4px solid #f97316;
        }
        .success { border-left-color: #10b981; }
        .error { border-left-color: #ef4444; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f97316;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
        }
        code {
            background: rgba(0,0,0,0.3);
            padding: 2px 6px;
            border-radius: 4px;
            color: #fbbf24;
        }
    </style>
</head>
<body>";

echo "<h1>🔍 Debug de Sesión Admin</h1>";

// Mostrar sesión actual
echo "<div class='info'>";
echo "<h2>📋 Sesión Actual</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

// Verificar usuario en BD
if (isset($_SESSION['usuario_id'])) {
    $userId = $_SESSION['usuario_id'];
    
    echo "<div class='info'>";
    echo "<h2>👤 Usuario en Base de Datos</h2>";
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, es_admin FROM usuario WHERE id = ?");
        $stmt->execute([$userId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo "<p><strong>ID:</strong> {$usuario['id']}</p>";
            echo "<p><strong>Username:</strong> " . ($usuario['username'] ?? 'NO DEFINIDO') . "</p>";
            echo "<p><strong>Email:</strong> {$usuario['email']}</p>";
            echo "<p><strong>Es Admin:</strong> " . ($usuario['es_admin'] ? 'SÍ ✅' : 'NO ❌') . "</p>";
            
            // Verificar si necesita actualizar sesión
            if ($usuario['es_admin'] == 1 && (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1)) {
                echo "<div class='info error'>";
                echo "<h3>⚠️ Problema Detectado</h3>";
                echo "<p>El usuario ES admin en la BD pero la sesión NO lo refleja.</p>";
                echo "<a href='?actualizar=1' class='btn'>🔄 Actualizar Sesión</a>";
                echo "</div>";
            } else if ($usuario['es_admin'] == 1) {
                echo "<div class='info success'>";
                echo "<h3>✅ Todo Correcto</h3>";
                echo "<p>El usuario es admin y la sesión está correcta.</p>";
                echo "<a href='carrusel_destacado.php' class='btn'>➡️ Ir al Panel Carrusel</a>";
                echo "</div>";
            } else {
                echo "<div class='info error'>";
                echo "<h3>❌ Usuario No es Admin</h3>";
                echo "<p>Este usuario no tiene permisos de administrador en la base de datos.</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='error'>❌ Usuario no encontrado en la base de datos</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
} else {
    echo "<div class='info error'>";
    echo "<h2>❌ No hay sesión iniciada</h2>";
    echo "<p>Debes iniciar sesión primero.</p>";
    echo "<a href='../login.php' class='btn'>🔐 Iniciar Sesión</a>";
    echo "</div>";
}

// Actualizar sesión si se solicita
if (isset($_GET['actualizar']) && isset($_SESSION['usuario_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, es_admin FROM usuario WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            $_SESSION['username'] = $usuario['username'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['es_admin'] = $usuario['es_admin'];
            
            echo "<div class='info success'>";
            echo "<h2>✅ Sesión Actualizada</h2>";
            echo "<p>La sesión se ha actualizado correctamente.</p>";
            echo "<a href='carrusel_destacado.php' class='btn'>➡️ Ir al Panel Carrusel</a>";
            echo "<a href='debug_sesion.php' class='btn'>🔄 Recargar</a>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='info error'>";
        echo "<h2>❌ Error al Actualizar</h2>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}

// Verificar todos los usuarios admin
echo "<div class='info'>";
echo "<h2>👥 Usuarios Admin en la BD</h2>";
try {
    $stmt = $pdo->query("SELECT id, username, email, es_admin FROM usuario WHERE es_admin = 1");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "<p>❌ No hay usuarios admin en la base de datos</p>";
    } else {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: rgba(255,255,255,0.1);'>";
        echo "<th style='padding: 10px; text-align: left;'>ID</th>";
        echo "<th style='padding: 10px; text-align: left;'>Username</th>";
        echo "<th style='padding: 10px; text-align: left;'>Email</th>";
        echo "</tr>";
        foreach ($admins as $admin) {
            echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.1);'>";
            echo "<td style='padding: 10px;'>{$admin['id']}</td>";
            echo "<td style='padding: 10px;'>" . ($admin['username'] ?? 'NO DEFINIDO') . "</td>";
            echo "<td style='padding: 10px;'>{$admin['email']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>🔗 Enlaces Útiles</h2>";
echo "<a href='../index.php' class='btn'>🏠 Inicio</a>";
echo "<a href='index.php' class='btn'>📊 Panel Admin</a>";
echo "<a href='carrusel_destacado.php' class='btn'>🎬 Panel Carrusel</a>";
echo "</div>";

echo "</body></html>";
?>
