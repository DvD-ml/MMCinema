<?php

require_once dirname(__DIR__, 2) . '/includes/optimizar_imagen.php';

if (!function_exists('mm_upload_image')) {
    function mm_upload_image(array $file, string $relativeDir, string $prefix = 'img', ?string $oldRelativePath = null): ?string
    {
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $oldRelativePath;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $oldRelativePath;
        }

        $basePath = dirname(__DIR__, 2);
        $relativeDir = trim(str_replace('\\', '/', $relativeDir), '/');
        $absoluteDir = $basePath . '/' . $relativeDir;

        $oldFileName = null;
        if (!empty($oldRelativePath)) {
            $oldRelativePath = trim(str_replace('\\', '/', $oldRelativePath), '/');
            $oldFileName = basename($oldRelativePath);
        }

        try {
            $nombreFinal = optimizarYGuardarWebp(
                $file,
                $absoluteDir,
                $prefix,
                72,
                1600,
                1600,
                $oldFileName
            );

            return $relativeDir . '/' . $nombreFinal;
        } catch (Throwable $e) {
            return $oldRelativePath;
        }
    }
}