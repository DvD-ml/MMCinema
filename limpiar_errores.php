<?php
/**
 * Script para limpiar errores de encoding y session_start duplicados
 */

// Mapeo completo de caracteres corruptos
$replacements = [
    'âce' => '✓',
    'â€¢' => '•',
    'â€"' => '–',
    'â€"' => '—',
    'â€˜' => ''',
    'â€™' => ''',
    'â€œ' => '"',
    'â€' => '"',
    'â„¢' => '™',
    'â„¢' => '®',
    'Â' => '',
    'Ã' => 'Á',
    'Ã¡' => 'á',
    'Ã©' => 'é',
    'Ã­' => 'í',
    'Ã³' => 'ó',
    'Ã¹' => 'ú',
    'Ã' => 'Á',
    'Ã‰' => 'É',
    'Ã' => 'Í',
    'Ã"' => 'Ó',
    'Ã"' => 'Ú',
    'Ã±' => 'ñ',
    'Ã' => 'Ñ',
    'CrÃ­ticas' => 'Críticas',
    'PelÃ­culas' => 'Películas',
    'PelÃ­cula' => 'Película',
    'PELÃCULAS' => 'PELÍCULAS',
    'PELÃ"XIMAMENTE' => 'PRÓXIMAMENTE',
    'PrÃ³ximamente' => 'Próximamente',
    'prÃ³ximos' => 'próximos',
    'aÃ±adido' => 'añadido',
    'TodavÃ­a' => 'Todavía',
    'crÃ­tica' => 'crítica',
    'valoraciÃ³n' => 'valoración',
    'Â·' => '·',
    'â€¹' => '‹',
    'â€º' => '›',
    'â˜…' => '★',
    'Ã—' => '×',
    'â‚¬' => '€',
    'CÃ³digo' => 'Código',
    'MÃXIMO' => 'MÁXIMO',
    'todavÃ­a' => 'todavía',
    'GestiÃ³n' => 'Gestión',
    'funciÃ³n' => 'función',
];

// Obtener todos los archivos PHP
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$totalFixed = 0;
$totalFiles = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        
        // Saltar archivos de script
        if (basename($filePath) === 'limpiar_errores.php') {
            continue;
        }
        
        $content = file_get_contents($filePath);
        $original = $content;
        
        // Reemplazar caracteres corruptos
        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        
        // Arreglar session_start() duplicados
        // Patrón: session_start(); ... session_start();
        $content = preg_replace('/session_start\(\);\s*\n\s*session_start\(\);/', 'session_start();', $content);
        
        // Arreglar múltiples session_start() al inicio
        if (preg_match_all('/session_start\(\);/', $content, $matches)) {
            if (count($matches[0]) > 1) {
                // Dejar solo el primero
                $lines = explode("\n", $content);
                $sessionStartCount = 0;
                $newLines = [];
                
                foreach ($lines as $line) {
                    if (strpos($line, 'session_start()') !== false) {
                        $sessionStartCount++;
                        if ($sessionStartCount === 1) {
                            $newLines[] = $line;
                        }
                    } else {
                        $newLines[] = $line;
                    }
                }
                
                $content = implode("\n", $newLines);
            }
        }
        
        if ($content !== $original) {
            file_put_contents($filePath, $content, LOCK_EX);
            echo "✅ Corregido: $filePath\n";
            $totalFixed++;
        }
        
        $totalFiles++;
    }
}

echo "\n✨ Proceso completado\n";
echo "Archivos procesados: $totalFiles\n";
echo "Archivos corregidos: $totalFixed\n";
?>
