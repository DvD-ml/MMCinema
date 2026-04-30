<?php
session_start();
require_once "../config/conexion.php";
require_once "../helpers/Logger.php";
require_once "../helpers/CSRF.php";
require_once "../helpers/RateLimiter.php";

$email = '';
$pass = '';
$recordar = false;

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
}

if (isset($_POST['password'])) {
    $pass = $_POST['password'];
}

if (isset($_POST['recordar'])) {
    if ($_POST['recordar'] === '1') {
        $recordar = true;
    }
}

CSRF::validarOAbortar();

if ($email === '' || $pass === '') {
    header("Location: ../pages/login.php?error=1");
    exit();
}

$estaBloqueado = RateLimiter::estaBloqueado($email);
if ($estaBloqueado) {
    $tiempoRestante = RateLimiter::getTiempoRestante($email);
    $minutosRestantes = ceil($tiempoRestante / 60);
    header("Location: ../pages/login.php?error=bloqueado&tiempo=" . $minutosRestantes);
    exit();
}

$sql = "SELECT id, username, email, password_hash, rol, verificado FROM usuario WHERE email = ? LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([$email]);
$user = $stm->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    RateLimiter::registrarIntentoFallido($email);
    header("Location: ../pages/login.php?error=1");
    exit();
}

$passwordCorrecta = password_verify($pass, $user['password_hash']);
if (!$passwordCorrecta) {
    RateLimiter::registrarIntentoFallido($email);
    header("Location: ../pages/login.php?error=1");
    exit();
}

// Verificación de email deshabilitada (emails no funcionan en este servidor)
// $usuarioVerificado = (int)$user['verificado'];
// if ($usuarioVerificado !== 1) {
//     header("Location: ../pages/login.php?error=no_verificado&email=" . urlencode($email));
//     exit();
// }

$_SESSION['usuario_id'] = (int)$user['id'];
$_SESSION['usuario'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['rol'] = $user['rol'];

RateLimiter::limpiarIntentos($email);

if ($recordar) {
    $token = bin2hex(random_bytes(32));
    $fechaActual = date('Y-m-d H:i:s');
    $fechaExpira = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $sql = "UPDATE usuario SET remember_token = ?, remember_expira = ? WHERE id = ?";
    $stmToken = $pdo->prepare($sql);
    $stmToken->execute([$token, $fechaExpira, $user['id']]);
    
    $tiempoExpiracion = time() + (30 * 24 * 60 * 60);
    
    setcookie(
        'remember_token',
        $token,
        [
            'expires' => $tiempoExpiracion,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

CSRF::regenerarToken();

usleep(100000);

header("Location: ../pages/index.php");
exit();
?>