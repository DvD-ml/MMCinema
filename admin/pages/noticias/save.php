<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../includes/optimizar_imagen.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Configurar variables para CRUD genérico
$entity = 'noticia';
$table = 'noticia';
$fields = ['titulo', 'contenido', 'imagen'];
$redirect = 'list.php';
$optionalFields = ['imagen'];

// Función para procesar imagen antes de guardar
$beforeSave = function(&$data, $pdo) {
    if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
        try {
            $imagenActual = $_POST['imagen_actual'] ?? '';
            $data['imagen'] = optimizarYGuardarWebp(
                $_FILES['imagen_file'],
                __DIR__ . '/../../../assets/img/noticias',
                'noticia_' . $data['titulo'],
                72,
                1400,
                900,
                $imagenActual !== '' ? $imagenActual : null
            );
        } catch (Throwable $e) {
            header("Location: list.php?error=imagen");
            exit();
        }
    } else {
        // Mantener imagen actual
        $data['imagen'] = $_POST['imagen_actual'] ?? '';
    }
};

// Función para ejecutar después de guardar (para insertar fecha de publicación)
$afterSave = function($data, $pdo) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        // Si es nuevo, actualizar fecha de publicación usando LAST_INSERT_ID
        $stm = $pdo->prepare("UPDATE noticia SET publicado = NOW() WHERE id = LAST_INSERT_ID()");
        $stm->execute();
    }
};

// Incluir CRUD genérico
require_once __DIR__ . "/../../../admin/crud/save.php";
?>





