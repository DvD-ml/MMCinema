<?php
session_start();
require_once "../config/conexion.php";
require_once "../helpers/Logger.php";
require_once "../helpers/CSRF.php";
require_once "../helpers/RateLimiter.php";

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';
$recordar = isset($_POST['recordar']) && $_POST['recordar'] === '1';

// Validar CSRF
CSRF::validarOAbortar();

if ($email === '' || $pass === '') {
    Logger::warning("Intento de login con campos vacíos", ['email' => $email]);
    header("Location: ../login.php?error=1");
    exit();
}

// Verificar si el usuario está bloqueado por rate limiting
if (RateLimiter::estaBloqueado($email)) {
    $tiempoRestante = RateLimiter::getTiempoRestante($email);
    Logger::security("Intento de login bloqueado por rate limiting", ['email' => $email, 'tiempo_restante' => $tiempoRestante]);
    header("Location: ../login.php?error=bloqueado&tiempo=" . ceil($tiempoRestante / 60));
    exit();
}

$stm = $pdo->prepare("
    SELECT id, username, email, password_hash, rol, verificado
    FROM usuario
    WHERE email = ?
    LIMIT 1
");
$stm->execute([$email]);
$user = $stm->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    Logger::security("Intento de login con email inexistente", ['email' => $email]);
    RateLimiter::registrarIntentoFallido($email);
    header("Location: ../login.php?error=1");
    exit();
}

if (!password_verify($pass, $user['password_hash'])) {
    Logger::security("Intento de login con contraseúa incorrecta", [
        'email' => $email,
        'user_id' => $user['id']
    ]);
    RateLimiter::registrarIntentoFallido($email);
    header("Location: ../login.php?error=1");
    exit();
}

if ((int)$user['verificado'] !== 1) {
    Logger::warning("Intento de login sin verificar email", ['user_id' => $user['id']]);
    header("Location: ../login.php?error=no_verificado&email=" . urlencode($email));
    exit();
}

// Login exitoso
$_SESSION['usuario_id'] = (int)$user['id'];
$_SESSION['usuario']    = $user['username'];
$_SESSION['email']      = $user['email'];
$_SESSION['rol']        = $user['rol'];

// Limpiar intentos fallidos
RateLimiter::limpiarIntentos($email);

Logger::info("Usuario inició sesión", [
    'user_id' => $user['id'],
    'username' => $user['username'],
    'recordar' => $recordar
]);

// Si marcó "Recordar sesión", crear cookie segura
if ($recordar) {
    // Generar token único y seguro
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Guardar token en BD
    $stmToken = $pdo->prepare("
        UPDATE usuario 
        SET remember_token = ?, remember_expira = ?
        WHERE id = ?
    ");
    $stmToken->execute([$token, $expira, $user['id']]);
    
    // Crear cookie segura (30 días)
    setcookie(
        'remember_token',
        $token,
        [
            'expires' => time() + (30 * 24 * 60 * 60), // 30 días
            'path' => '/',
            'domain' => '',
            'secure' => false, // Cambiar a true en producción con HTTPS
            'httponly' => true, // No accesible desde JavaScript
            'samesite' => 'Lax' // Protección CSRF
        ]
    );
    
    Logger::info("Cookie de recordar sesión creada", ['user_id' => $user['id']]);
}

// Regenerar token CSRF después del login
CSRF::regenerarToken();

// Pequeúa pausa para que el navegador detecte el login exitoso
// y ofrezca guardar la contraseúa
usleep(100000); // 100ms

header("Location: ../index.php");
exit();