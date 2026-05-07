<?php
require_once __DIR__ . '/../../config/conexion.php';

$pdo->exec("ALTER TABLE usuario MODIFY username VARCHAR(50) NOT NULL, MODIFY email VARCHAR(50) NOT NULL");
echo "DB OK\n";
