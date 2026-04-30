-- Agregar columna para la posición de la imagen de fondo
ALTER TABLE carrusel_destacado 
ADD COLUMN imagen_posicion VARCHAR(50) DEFAULT 'center' AFTER imagen_fondo;

-- Actualizar slides existentes con posición por defecto
UPDATE carrusel_destacado SET imagen_posicion = 'center' WHERE imagen_posicion IS NULL;