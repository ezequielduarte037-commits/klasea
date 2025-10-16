-- Base de datos para sistema de login Klase A
-- Panel de Propietarios

CREATE DATABASE IF NOT EXISTS klasea_clients CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE klasea_clients;

-- Tabla de usuarios (propietarios)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    modelo_barco VARCHAR(10) NOT NULL COMMENT 'Modelo: 85, 64, 52, 43, 42, 37, 34',
    imagen_unidad VARCHAR(500) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_email (email),
    INDEX idx_modelo (modelo_barco)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios de ejemplo (contraseña: klase2025)
INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES
('Juan Pérez', 'juan.perez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '42', 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=1200'),
('María González', 'maria.gonzalez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '64', 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?w=1200'),
('Carlos Rodríguez', 'carlos.rodriguez@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '85', 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=1200');

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin de ejemplo (usuario: admin, contraseña: admin2025)
INSERT INTO administradores (usuario, password, nombre, email) VALUES
('admin', '$2y$10$7KJQGr3K3qE.HYKZqQJPu.Ej0KjK3h8QFPmF3Oi8LQWwYZHG5L5Uu', 'Administrador', 'admin@klasea.com');

-- Tabla para registro de sesiones
CREATE TABLE IF NOT EXISTS sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
