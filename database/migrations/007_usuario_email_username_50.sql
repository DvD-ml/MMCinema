-- Ajusta los limites de registro a 50 caracteres.
ALTER TABLE `usuario`
MODIFY `username` VARCHAR(50) NOT NULL,
MODIFY `email` VARCHAR(50) NOT NULL;