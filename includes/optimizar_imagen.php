<?php

function mm_slug_nombre_archivo(string $texto): string
{
    $texto = trim($texto);

    if ($texto === '') {
        return 'imagen';
    }

    if (function_exists('iconv')) {
        $transliterado = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        if ($transliterado !== false && $transliterado !== '') {
            $texto = $transliterado;
        }
    }

    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    $texto = trim($texto, '-');

    return $texto !== '' ? $texto : 'imagen';
}

function mm_borrar_archivo_si_existe(string $ruta): void
{
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

function mm_crear_imagen_desde_archivo(string $tmpPath, string $mime)
{
    switch ($mime) {
        case 'image/jpeg':
            if (!function_exists('imagecreatefromjpeg')) {
                throw new Exception("El servidor no soporta leer imagenes JPG con GD.");
            }
            return imagecreatefromjpeg($tmpPath);

        case 'image/png':
            if (!function_exists('imagecreatefrompng')) {
                throw new Exception("El servidor no soporta leer imagenes PNG con GD.");
            }
            return imagecreatefrompng($tmpPath);

        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) {
                throw new Exception("El servidor no soporta leer imagenes WebP con GD.");
            }
            return imagecreatefromwebp($tmpPath);

        case 'image/avif':
            if (function_exists('imagecreatefromavif')) {
                return imagecreatefromavif($tmpPath);
            }
            throw new Exception("Tu servidor no soporta leer imágenes AVIF.");

        default:
            throw new Exception("Formato no permitido. Solo JPG, PNG, WEBP o AVIF.");
    }
}

function optimizarYGuardarWebp(
    array $file,
    string $carpetaDestino,
    string $nombreBase,
    int $calidad = 72,
    int $maxAncho = 1200,
    int $maxAlto = 1600,
    ?string $archivoAnterior = null
): string {
    if (!extension_loaded('gd')) {
        throw new Exception("El servidor no tiene activada la extension GD de PHP.");
    }

    if (!function_exists('imagewebp')) {
        throw new Exception("El servidor no soporta guardar imagenes WebP. Activa GD con soporte WebP.");
    }

    if (
        !isset($file['tmp_name']) ||
        !isset($file['error']) ||
        $file['error'] !== UPLOAD_ERR_OK ||
        !is_uploaded_file($file['tmp_name'])
    ) {
        throw new Exception("No se ha subido ninguna imagen válida.");
    }

    if (!is_dir($carpetaDestino)) {
        if (!mkdir($carpetaDestino, 0777, true)) {
            throw new Exception("No se pudo crear la carpeta destino.");
        }
    }

    if (!is_writable($carpetaDestino)) {
        throw new Exception("La carpeta destino no tiene permisos de escritura: " . $carpetaDestino);
    }

    $info = getimagesize($file['tmp_name']);
    if ($info === false) {
        throw new Exception("El archivo no es una imagen válida.");
    }

    $mime = $info['mime'];
    $anchoOriginal = (int)$info[0];
    $altoOriginal = (int)$info[1];

    if ($anchoOriginal <= 0 || $altoOriginal <= 0) {
        throw new Exception("La imagen tiene dimensiones no válidas.");
    }

    $imagenOriginal = mm_crear_imagen_desde_archivo($file['tmp_name'], $mime);

    if (!$imagenOriginal) {
        throw new Exception("No se pudo procesar la imagen.");
    }

    $ratio = $anchoOriginal / $altoOriginal;
    $nuevoAncho = $anchoOriginal;
    $nuevoAlto = $altoOriginal;

    if ($nuevoAncho > $maxAncho) {
        $nuevoAncho = $maxAncho;
        $nuevoAlto = (int) round($nuevoAncho / $ratio);
    }

    if ($nuevoAlto > $maxAlto) {
        $nuevoAlto = $maxAlto;
        $nuevoAncho = (int) round($nuevoAlto * $ratio);
    }

    $imagenNueva = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

    // Preservar transparencia para PNG y WebP
    imagealphablending($imagenNueva, false);
    imagesavealpha($imagenNueva, true);
    $transparent = imagecolorallocatealpha($imagenNueva, 0, 0, 0, 127);
    imagefill($imagenNueva, 0, 0, $transparent);
    imagealphablending($imagenNueva, true);

    imagecopyresampled(
        $imagenNueva,
        $imagenOriginal,
        0,
        0,
        0,
        0,
        $nuevoAncho,
        $nuevoAlto,
        $anchoOriginal,
        $altoOriginal
    );

    $nombreSeguro = mm_slug_nombre_archivo($nombreBase);
    $nombreFinal = $nombreSeguro . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.webp';
    $rutaFinal = rtrim($carpetaDestino, '/\\') . DIRECTORY_SEPARATOR . $nombreFinal;

    if (!imagewebp($imagenNueva, $rutaFinal, $calidad)) {
        imagedestroy($imagenOriginal);
        imagedestroy($imagenNueva);
        throw new Exception("No se pudo guardar la imagen en formato WEBP.");
    }

    imagedestroy($imagenOriginal);
    imagedestroy($imagenNueva);

    if (!empty($archivoAnterior)) {
        $rutaAnterior = rtrim($carpetaDestino, '/\\') . DIRECTORY_SEPARATOR . $archivoAnterior;
        mm_borrar_archivo_si_existe($rutaAnterior);
    }

    return $nombreFinal;
}
