<?php
/**
 * Archivo de entrada principal
 * Redirige a pages/index.php manteniendo las rutas correctas
 */

// Definir rutas absolutas
define('BASE_PATH', __DIR__);
define('CONFIG_PATH', BASE_PATH . '/config');
define('PAGES_PATH', BASE_PATH . '/pages');

require_once PAGES_PATH . '/index.php';
