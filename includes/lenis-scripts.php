<?php
// Detectar la ruta base dinámicamente
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePathForAssets = '';

if (strpos($scriptDir, '/pages') !== false || strpos($scriptDir, '/admin') !== false) {
    $basePathForAssets = '../';
}
?>
<!-- Lenis Smooth Scroll -->
<script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
<script src="<?= $basePathForAssets ?>assets/js/lenis-init.js"></script>
