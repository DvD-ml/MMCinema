-- Script para actualizar límites de caracteres en tabla usuario
-- Fecha: 30 de Abril de 2026
-- Cambios: username de 100 a 25 caracteres, email de 100 a 50 caracteres

ALTER TABLE `usuario` 
MODIFY `username` VARCHAR(25) NOT NULL,
MODIFY `email` VARCHAR(50) NOT NULL;

-- Verificar cambios
SHOW COLUMNS FROM `usuario` WHERE Field IN ('username', 'email');
