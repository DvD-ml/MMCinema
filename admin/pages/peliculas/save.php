<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();

require_once __DIR__ . "/../../../config/conexion.php";
require_once __DIR__ . "/../../../includes/optimizar_imagen.php";
require_once __DIR__ . "/../../../helpers/CSRF.php";

// Configurar variables para CRUD genérico
$entity = 'pelicula';
$table = 'pelicula';
$fields = ['titulo', 'sinopsis', 'poster', 'fecha_estreno', 'duracion', 'edad', 'id_genero', 'trailer'];
$redirect = 'list.php';
$optionalFields = ['trailer'];

// Función para procesar imagen antes de guardar
$beforeSave = function(&$data, $pdo) {
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        try {
            $posterActual = $_POST['poster_actual'] ?? '';
            $data['poster'] = optimizarYGuardarWebp(
                $_FILES['poster_file'],
                __DIR__ . '/../../../assets/img/posters',
                'pelicula_' . $data['titulo'],
                70,
                900,
                1350,
                $posterActual !== '' ? $posterActual : null
            );
        } catch (Throwable $e) {
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





