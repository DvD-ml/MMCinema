<?php
/**
 * Script para agregar la columna es_admin y crear usuarios administradores
 */

require_once "config/conexion.php";

echo "<h1>Reparación del Sistema de Administradores</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

try {
    echo "<h2>Paso 1: Verificar estructura actual</h2>";
    
    // Verificar si la columna es_admin ya existe
    $columns = $pdo->query("DESCRIBE usuario")->fetchAll(PDO::FETCH_ASSOC);
    $tiene_es_admin = false;
    
    echo "Columnas actuales en la tabla usuario:<br>";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
        if ($col['Field'] === 'es_admin') {
            $tiene_es_admin = true;
        }
    }
    
    echo "<h2>Paso 2: Agregar columna es_admin</h2>";
    
    if (!$tiene_es_admin) {
        $pdo->exec("ALTER TABLE usuario ADD COLUMN es_admin TINYINT(1) DEFAULT 0 AFTER password");
        echo "✅ Columna 'es_admin' agregada correctamente<br>";
    } else {
        echo "✅ La columna 'es_admin' ya existe<br>";
    }
    
    echo "<h2>Paso 3: Crear usuario administrador por defecto</h2>";
    
    // Verificar si ya existe el admin
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = 'admin@mmcinema.com'");
    $stmt->execute();
    $admin_existe = $stmt->fetch();
    
    if (!$admin_existe) {
        // Crear usuario admin
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuario (username, email, password, es_admin, verificado) VALUES (?, ?, ?, 1, 1)");
        $stmt->execute(['admin', 'admin@mmcinema.com', $password_hash]);
        echo "✅ Usuario administrador creado: admin@mmcinema.com / admin123<br>";
    } else {
        // Asegurar que sea admin
        $pdo->prepare("UPDATE usuario SET es_admin = 1 WHERE email = 'admin@mmcinema.com'")->execute();
        echo "✅ Usuario admin@mmcinema.com ya existe y tiene permisos de admin<br>";
    }
    
    echo "<h2>Paso 4: Hacer tu usuario administrador</h2>";
    
    // Hacer que el usuario actual también sea admin
    $stmt = $pdo->prepare("UPDATE usuario SET es_admin = 1 WHERE email = 'david.monzonlopez@gmail.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Tu usuario (david.monzonlopez@gmail.com) ahora es administrador<br>";
    } else {
        echo "⚠️ No se pudo actualizar tu usuario (puede que no exista con ese email)<br>";
    }
    
    echo "<h2>Paso 5: Verificar administradores</h2>";
    
    $admins = $pdo->query("SELECT id, username, email, es_admin FROM usuario WHERE es_admin = 1")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Usuarios administradores:<br>";
    foreach ($admins as $admin) {
        echo "- ID: {$admin['id']}, Username: {$admin['username']}, Email: {$admin['email']}<br>";
    }
    
    echo "<h2>✅ Reparación Completada</h2>";
    echo "Ahora puedes:<br>";
    echo "1. <a href='logout.php'>Cerrar sesión</a> y volver a iniciar sesión<br>";
    echo "2. O <a href='admin/carrusel_destacado.php'>Acceder directamente al panel del carrusel</a><br>";
    echo "3. <a href='admin/debug_carrusel.php'>Verificar de nuevo el estado</a><br>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Código de error: " . $e->getCode() . "<br>";
}
?>