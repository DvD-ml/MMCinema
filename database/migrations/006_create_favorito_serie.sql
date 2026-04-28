-- Tabla para favoritos de series
CREATE TABLE IF NOT EXISTS `favorito_serie` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_serie` INT(11) NOT NULL,
  `creado` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito_serie` (`id_usuario`, `id_serie`),
  KEY `idx_usuario_serie` (`id_usuario`),
  KEY `idx_serie` (`id_serie`),
  CONSTRAINT `fk_favorito_serie_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorito_serie_serie` FOREIGN KEY (`id_serie`) REFERENCES `serie` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
