<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../admin/helpers/upload_helper.php";
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
            $rutaImagen = mm_upload_image(
                $_FILES['imagen_file'],
                'assets/img/noticias',
                'noticia_' . $data['titulo'],
                $imagenActual
            );
            $data['imagen'] = basename((string)$rutaImagen);
        } catch (Throwable $e) {
            error_log("Error procesando imagen de noticia: " . $e->getMessage());
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





