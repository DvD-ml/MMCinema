-- Script para agregar las nuevas categorías al campo ENUM de carrusel_destacado
-- Ejecutar en phpMyAdmin en la base de datos mmcinema3

ALTER TABLE `carrusel_destacado` 
MODIFY COLUMN `categoria` ENUM(
    'destacada',
    'mejores',
    'proximamente',
    'nueva_temporada',
    'nuevo_episodio',
    'nuevos',
    'populares'
) DEFAULT 'destacada';

-- Verificar que se agregaron correctamente
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'mmcinema3' 
  AND TABLE_NAME = 'carrusel_destacado' 
  AND COLUMN_NAME = 'categoria';
