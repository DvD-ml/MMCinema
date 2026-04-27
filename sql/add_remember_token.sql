-- Agregar columnas para "Recordar sesión"
-- Ejecutar en phpMyAdmin o línea de comandos

ALTER TABLE `usuario` 
ADD COLUMN `remember_token` VARCHAR(64) NULL DEFAULT NULL AFTER `reset_expira`,
ADD COLUMN `remember_expira` DATETIME NULL DEFAULT NULL AFTER `remember_token`,
ADD INDEX `idx_remember_token` (`remember_token`);
