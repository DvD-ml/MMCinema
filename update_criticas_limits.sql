-- Script para actualizar límites de críticas
-- Fecha: 30 de Abril de 2026
-- Cambios: Aumentar contenido de críticas a TEXT para soportar más palabras

-- Verificar estructura actual
SHOW COLUMNS FROM `critica`;
SHOW COLUMNS FROM `critica_serie`;

-- Los campos ya son TEXT, así que no necesitan cambios en BD
-- El límite de 150 palabras se validará en PHP/JavaScript
