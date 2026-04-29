<?php
require_once "auth.php";
verificarAuth();

require_once "../config/conexion.php";

echo "<h1>Debug - Acceso al Carrusel</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

echo "<h2>Estado de la Sesión:</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Usuario ID: " . ($_SESSION['usuario_id'] ?? 'NO DEFINIDO') . "<br>";
echo "Username: " . ($_SESSION['username'] ?? 'NO DEFINIDO') . "<br>";
echo "Email: " . ($_SESSION['email'] ?? 'NO DEFINIDO') . "<br>";
echo "Es Admin: " . ($_SESSION['es_admin'] ?? 'NO DEFINIDO') . "<br>";

echo "<h2>Verificaciones:</h2>";

// Verificar si hay sesión iniciada
if (!isset($_SESSION['usuario_id'])) {
    echo "❌ No hay usuario logueado<br>";
    echo "→ Debes iniciar sesión primero<br>";
    echo "→ <a href='../login.php'>Ir al login</a><br>";
} else {
    echo "✅ Usuario logueado correctamente<br>";
    
    // Verificar si es admin
    if (!isset($_SESSION['es_admin']) || $_SESSION['es_admin'] != 1) {
        echo "❌ El usuario NO es administrador<br>";
        echo "→ es_admin = " . ($_SESSION['es_admin'] ?? 'null') . "<br>";
        echo "→ Solo los administradores pueden acceder al panel<br>";
    } else {
        echo "✅ Usuario es administrador<br>";
        echo "→ Puedes acceder al panel del carrusel<br>";
        echo "→ <a href='carrusel_destacado.php'>Ir al panel del carrusel</a><br>";
    }
}

echo "<h2>Datos de la BD:</h2>";
try {
    if (isset($_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("SELECT id, username, email, es_admin FROM usuario WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo "✅ Usuario encontrado en BD:<br>";
            echo "- ID: " . $usuario['id'] . "<br>";
            echo "- Username: " . $usuario['username'] . "<br>";
            echo "- Email: " . $usuario['email'] . "<br>";
            echo "- Es Admin (BD): " . $usuario['es_admin'] . "<br>";
            
            if ($usuario['es_admin'] != $_SESSION['es_admin']) {
                echo "⚠️ INCONSISTENCIA: es_admin en sesión (" . $_SESSION['es_admin'] . ") != es_admin en BD (" . $usuario['es_admin'] . ")<br>";
            }
        } else {
            echo "❌ Usuario no encontrado en BD<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error consultando BD: " . $e->getMessage() . "<br>";
}

echo "<h2>Soluciones:</h2>";
echo "1. Si no estás logueado: <a href='../login.php'>Iniciar sesión</a><br>";
echo "2. Si no eres admin: Contacta al administrador del sistema<br>";
echo "3. Credenciales de admin por defecto: admin@mmcinema.com / admin123<br>";
echo "4. <a href='../index.php'>Volver al inicio</a><br>";
?>