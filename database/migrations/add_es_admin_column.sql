-- Agregar la columna es_admin a la tabla usuario
ALTER TABLE usuario ADD COLUMN es_admin TINYINT(1) DEFAULT 0 AFTER password;

-- Crear el usuario administrador por defecto
INSERT IGNORE INTO usuario (username, email, password, es_admin, verificado) 
VALUES ('admin', 'admin@mmcinema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Hacer que tu usuario actual también sea administrador
UPDATE usuario SET es_admin = 1 WHERE email = 'david.monzonlopez@gmail.com';

-- Verificar los cambios
SELECT id, username, email, es_admin, verificado FROM usuario WHERE es_admin = 1;