-- Tabla para gestionar el carrusel destacado del home
CREATE TABLE IF NOT EXISTS carrusel_destacado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo ENUM('pelicula', 'serie') NOT NULL,
    id_contenido INT NOT NULL,
    imagen_fondo VARCHAR(255) NOT NULL,
    logo_titulo VARCHAR(255) NULL,
    categoria ENUM('destacada', 'mejores', 'proximamente', 'nuevos', 'populares') DEFAULT 'destacada',
    descripcion TEXT NULL,
    activo TINYINT(1) DEFAULT 1,
    orden INT DEFAULT 0,
    fecha_inicio DATE NULL,
    fecha_fin DATE NULL,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_activo (activo),
    INDEX idx_categoria (categoria),
    INDEX idx_orden (orden),
    INDEX idx_tipo_contenido (tipo, id_contenido)
);

-- Insertar algunos ejemplos por defecto
INSERT INTO carrusel_destacado (titulo, tipo, id_contenido, imagen_fondo, categoria, descripcion, orden) VALUES
('The Batman', 'pelicula', 1, 'carrusel_batman.jpg', 'destacada', 'El nuevo caballero de la noche llega a Gotham', 1),
('Deadpool y Lobezno', 'pelicula', 2, 'carrusel_deadpool.jpg', 'mejores', 'La dupla más esperada del año', 2),
('Toy Story 5', 'pelicula', 3, 'carrusel_toystory.jpg', 'proximamente', 'Los juguetes regresan en una nueva aventura', 3);