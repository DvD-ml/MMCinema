<?php
require_once __DIR__ . "/../../../admin/auth.php";
verificarAuth();
require_once __DIR__ . "/../../../config/conexion.php";

$entity = 'noticia';
$table = 'noticia';
$redirect = 'list.php';

$beforeDelete = function($record, $pdo) {
    $fileName = basename((string)($record['imagen'] ?? ''));
    if ($fileName !== '') {
        $path = __DIR__ . "/../../../assets/img/noticias/" . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }
};

require_once __DIR__ . "/../../../admin/crud/delete.php";
?>
