<?php
session_start();
require_once "../config/conexion.php";
require_once "../helpers/Logger.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$serie_id = isset($_POST['serie_id']) ? (int)$_POST['serie_id'] : 0;
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'series.php';

// Limpiar el redirect para evitar duplicación de rutas
$redirect = str_replace('/david/MMCINEMA/', '', $redirect);
$redirect = ltrim($redirect, '/');

if ($serie_id <= 0) {
    Logger::warning("Intento de toggle favorito serie con ID inválido", [
        'user_id' => $usuario_id,
        'serie_id' => $serie_id
    ]);
    header("Location: ../$redirect");
    exit();
}

try {
    // Verificar si ya existe en favoritos
    $stmCheck = $pdo->prepare("
        SELECT id FROM favorito_serie 
        WHERE id_usuario = ? AND id_serie = ?
        LIMIT 1
    ");
    $stmCheck->execute([$usuario_id, $serie_id]);
    $existe = $stmCheck->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        // Quitar de favoritos
        $stmDelete = $pdo->prepare("
            DELETE FROM favorito_serie 
            WHERE id_usuario = ? AND id_serie = ?
        ");
        $stmDelete->execute([$usuario_id, $serie_id]);
        
        Logger::info("Serie quitada de favoritos", [
            'user_id' => $usuario_id,
            'serie_id' => $serie_id
        ]);
    } else {
        // Agregar a favoritos
        $stmInsert = $pdo->prepare("
            INSERT INTO favorito_serie (id_usuario, id_serie, creado)
            VALUES (?, ?, NOW())
        ");
        $stmInsert->execute([$usuario_id, $serie_id]);
        
        Logger::info("Serie agregada a favoritos", [
            'user_id' => $usuario_id,
            'serie_id' => $serie_id
        ]);
    }

    header("Location: ../$redirect");
    exit();

} catch (PDOException $e) {
    Logger::error("Error al toggle favorito serie", [
        'user_id' => $usuario_id,
        'serie_id' => $serie_id,
        'error' => $e->getMessage()
    ]);
    
    header("Location: ../$redirect");
    exit();
}
