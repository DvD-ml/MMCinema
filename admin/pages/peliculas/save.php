<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../admin/helpers/upload_helper.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Configurar variables para CRUD genérico
$entity = 'pelicula';
$table = 'pelicula';
$fields = ['titulo', 'sinopsis', 'poster', 'fecha_estreno', 'duracion', 'edad', 'id_genero', 'trailer'];
$redirect = 'list.php';
$optionalFields = ['poster', 'trailer'];

// Función para procesar imagen antes de guardar
$beforeSave = function(&$data, $pdo) {
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        try {
            $posterActual = $_POST['poster_actual'] ?? '';
            $rutaPoster = mm_upload_image(
                $_FILES['poster_file'],
                'assets/img/posters',
                'pelicula_' . $data['titulo'],
                $posterActual
            );
            $data['poster'] = basename((string)$rutaPoster);
        } catch (Throwable $e) {
            error_log("Error procesando poster de pelicula: " . $e->getMessage());
            header("Location: list.php?error=imagen");
            exit();
        }
    } else {
        // Mantener poster actual
        $data['poster'] = $_POST['poster_actual'] ?? '';
    }
};

// Incluir CRUD genérico
require_once __DIR__ . "/../../../admin/crud/save.php";
?>





