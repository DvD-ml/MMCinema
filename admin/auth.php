<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    empty($_SESSION['usuario_id']) ||
    !isset($_SESSION['rol']) ||
    $_SESSION['rol'] !== 'admin'
) {
    header("Location: ../login.php?error=acceso");
    exit();
}
?>