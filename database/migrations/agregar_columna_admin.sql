-- Agregar columna es_admin a la tabla usuario si no existe
ALTER TABLE usuario ADD COLUMN IF NOT EXISTS es_admin TINYINT(1) DEFAULT 0;

-- Hacer que tu usuario (ID 17) sea administrador
UPDATE usuario SET es_admin = 1 WHERE id = 17;

-- También crear el usuario admin por defecto si no existe
INSERT IGNORE INTO usuario (username, email, password, es_admin, verificado) 
VALUES ('admin', 'admin@mmcinema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Verificar los usuarios admin
SELECT id, username, email, es_admin FROM usuario WHERE es_admin = 1;