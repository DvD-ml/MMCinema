<?php
/**
 * Validar tipo MIME de archivo
 */
function validarTipoMIME($tmpFile, $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp']) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpFile);
    finfo_close($finfo);
    
    return in_array($mime, $tiposPermitidos);
}

/**
 * Obtener mensaje de error para tipo MIME inválido
 */
function obtenerErrorTipoMIME() {
    return 'El archivo debe ser una imagen (JPEG, PNG o WebP)';
}
?>