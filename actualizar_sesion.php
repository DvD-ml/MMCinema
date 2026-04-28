<?php
/**
 * Script para actualizar la sesión con los permisos de admin
 */

session_start();
require_once "config/conexion.php";

echo "<h1>Actualizar Sesión de Administrador</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

if (!isset($_SESSION['usuario_id'])) {
    echo "❌ No hay sesión activa. <a href='login.php'>Inicia sesión</a><br>";
    exit;
}

try {
    $usuario_id = $_SESSION['usuario_id'];
    
    echo "<h2>Estado Actual de la Sesión:</h2>";
    echo "Usuario ID: $usuario_id<br>";
    echo "Es Admin (sesión): " . ($_SESSION['es_admin'] ?? 'NO DEFINIDO') . "<br>";
    
    // Obtener datos actualizados de la BD
    $stmt = $pdo->prepare("SELECT id, username, email, es_admin, verificado FROM usuario WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "❌ Usuario no encontrado en BD<br>";
        exit;
    }
    
    echo "<h2>Datos en la Base de Datos:</h2>";
    echo "Username: " . $usuario['username'] . "<br>";
    echo "Email: " . $usuario['email'] . "<br>";
    echo "Es Admin (BD): " . $usuario['es_admin'] . "<br>";
    echo "Verificado: " . $usuario['verificado'] . "<br>";
    
    // Actualizar la sesión con los datos de la BD
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['es_admin'] = (int)$usuario['es_admin'];
    $_SESSION['verificado'] = (int)$usuario['verificado'];
    
    echo "<h2>✅ Sesión Actualizada</h2>";
    echo "Nuevos valores en la sesión:<br>";
    echo "Username: " . $_SESSION['username'] . "<br>";
    echo "Email: " . $_SESSION['email'] . "<br>";
    echo "Es Admin: " . $_SESSION['es_admin'] . "<br>";
    
    if ($_SESSION['es_admin'] == 1) {
        echo "<br>🎉 <strong>¡Perfecto! Ahora eres administrador</strong><br>";
        echo "<br>Puedes acceder a:<br>";
        echo "- <a href='admin/carrusel_destacado.php'>Panel del Carrusel</a><br>";
        echo "- <a href='admin/index.php'>Panel de Administración</a><br>";
        echo "- <a href='admin/debug_carrusel.php'>Verificar estado</a><br>";
    } else {
        echo "<br>❌ Aún no tienes permisos de administrador<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>