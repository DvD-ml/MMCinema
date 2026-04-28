<?php
/**
 * Script para agregar la columna es_admin y configurar administradores
 */

require_once "config/conexion.php";

echo "<h1>Configuración de Administradores</h1>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; background: #1a1a1a; color: #fff; } h1, h2 { color: #e50914; }</style>";

try {
    echo "<h2>1. Verificando estructura de la tabla usuario...</h2>";
    
    // Verificar si la columna es_admin existe
    $columns = $pdo->query("DESCRIBE usuario")->fetchAll(PDO::FETCH_ASSOC);
    $tiene_es_admin = false;
    
    echo "Columnas actuales:<br>";
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
        if ($col['Field'] === 'es_admin') {
            $tiene_es_admin = true;
        }
    }
    
    if (!$tiene_es_admin) {
        echo "<br>❌ La columna 'es_admin' NO existe<br>";
        echo "✅ Agregando columna 'es_admin'...<br>";
        
        $pdo->exec("ALTER TABLE usuario ADD COLUMN es_admin TINYINT(1) DEFAULT 0");
        echo "✅ Columna 'es_admin' agregada correctamente<br>";
    } else {
        echo "<br>✅ La columna 'es_admin' ya existe<br>";
    }
    
    echo "<h2>2. Configurando tu usuario como administrador...</h2>";
    
    // Hacer que el usuario ID 17 sea admin
    $stmt = $pdo->prepare("UPDATE usuario SET es_admin = 1 WHERE id = 17");
    $stmt->execute();
    
    echo "✅ Usuario ID 17 configurado como administrador<br>";
    
    echo "<h2>3. Creando usuario admin por defecto...</h2>";
    
    // Crear usuario admin por defecto
    try {
        $stmt = $pdo->prepare("INSERT INTO usuario (username, email, password, es_admin, verificado) VALUES (?, ?, ?, 1, 1)");
        $stmt->execute(['admin', 'admin@mmcinema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);
        echo "✅ Usuario admin creado correctamente<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "⚠️ Usuario admin ya existe<br>";
        } else {
            echo "❌ Error creando usuario admin: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<h2>4. Verificando usuarios administradores...</h2>";
    
    $admins = $pdo->query("SELECT id, username, email, es_admin FROM usuario WHERE es_admin = 1")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "❌ No hay usuarios administradores<br>";
    } else {
        echo "✅ Usuarios administradores encontrados:<br>";
        foreach ($admins as $admin) {
            echo "- ID: {$admin['id']}, Username: {$admin['username']}, Email: {$admin['email']}<br>";
        }
    }
    
    echo "<h2>✅ Configuración completada</h2>";
    echo "Ahora puedes:<br>";
    echo "1. <a href='logout.php'>Cerrar sesión</a> y volver a iniciar sesión<br>";
    echo "2. O <a href='admin/carrusel_destacado.php'>Ir directamente al panel del carrusel</a><br>";
    echo "3. <a href='admin/debug_carrusel.php'>Verificar el estado nuevamente</a><br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>