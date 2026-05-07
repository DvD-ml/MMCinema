<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

$entity = 'pelicula';
$table = 'pelicula';
$redirect = 'list.php';

$beforeDelete = function($record, $pdo) {
    $fileName = basename((string)($record['poster'] ?? ''));
    if ($fileName !== '') {
        $path = __DIR__ . "/../../../assets/img/posters/" . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }
};

require_once __DIR__ . "/../../../admin/crud/delete.php";
?>
