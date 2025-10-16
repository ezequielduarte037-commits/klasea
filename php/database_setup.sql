-- Crear base de datos
CREATE DATABASE IF NOT EXISTS klasea_clients CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE klasea_clients;

-- Crear tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    modelo_barco VARCHAR(10) NOT NULL,
    imagen_unidad VARCHAR(500) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Insertar usuarios de ejemplo
INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES 
('Carlos Mendoza', 'carlos.mendoza@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '85', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'),
('Ana Rodríguez', 'ana.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '64', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'),
('Roberto Silva', 'roberto.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '52', 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'),
('María González', 'maria.gonzalez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '43', 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'),
('Jorge López', 'jorge.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '42', 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');

-- Nota: La contraseña para todos los usuarios de ejemplo es "password123"