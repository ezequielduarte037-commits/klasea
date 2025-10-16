-- Base de datos para Panel Operativo Editorial
CREATE DATABASE IF NOT EXISTS `panel_operaciones` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `panel_operaciones`;

-- Tabla de usuarios
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` enum('admin','editor','operador') DEFAULT 'operador',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de sesiones
CREATE TABLE `sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `token` (`token`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de operaciones (ejemplo)
CREATE TABLE `operaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `tipo` enum('publicacion','revision','edicion','diseno') DEFAULT 'publicacion',
  `estado` enum('pendiente','en_proceso','completado','cancelado') DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `usuario_asignado` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_completado` timestamp NULL DEFAULT NULL,
  `comentarios` text,
  PRIMARY KEY (`id`),
  KEY `usuario_asignado` (`usuario_asignado`),
  FOREIGN KEY (`usuario_asignado`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabla de métricas (para los gráficos)
CREATE TABLE `metricas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fecha` (`fecha`),
  KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insertar usuario administrador por defecto
INSERT INTO `usuarios` (`username`, `password`, `email`, `nombre`, `rol`, `activo`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@panel.com', 'Administrador', 'admin', 1);

-- Insertar datos de ejemplo para operaciones
INSERT INTO `operaciones` (`titulo`, `descripcion`, `tipo`, `estado`, `prioridad`, `fecha_vencimiento`) VALUES
('Revisión de artículo sobre tecnología', 'Revisar y corregir artículo sobre las últimas tendencias tecnológicas', 'revision', 'pendiente', 'alta', '2024-02-15'),
('Diseño de portada revista', 'Crear diseño para la portada de la revista de marzo', 'diseno', 'en_proceso', 'media', '2024-02-20'),
('Edición de entrevista', 'Editar entrevista con el CEO de la empresa', 'edicion', 'pendiente', 'baja', '2024-02-25'),
('Publicación de noticias', 'Publicar noticias del sector editorial', 'publicacion', 'completado', 'media', '2024-02-10');

-- Insertar métricas de ejemplo
INSERT INTO `metricas` (`fecha`, `tipo`, `valor`, `descripcion`) VALUES
('2024-02-01', 'operaciones_completadas', 15, 'Operaciones completadas en febrero'),
('2024-02-01', 'operaciones_pendientes', 8, 'Operaciones pendientes'),
('2024-02-01', 'tiempo_promedio', 2.5, 'Tiempo promedio en días'),
('2024-02-01', 'eficiencia', 85.5, 'Porcentaje de eficiencia'),
('2024-01-31', 'operaciones_completadas', 12, 'Operaciones completadas en enero'),
('2024-01-31', 'operaciones_pendientes', 5, 'Operaciones pendientes'),
('2024-01-31', 'tiempo_promedio', 3.2, 'Tiempo promedio en días'),
('2024-01-31', 'eficiencia', 78.3, 'Porcentaje de eficiencia');