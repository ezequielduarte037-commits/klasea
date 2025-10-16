-- Base de datos para el sistema de panel operativo Klase A
CREATE DATABASE IF NOT EXISTS klasea_clients CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE klasea_clients;

-- Tabla de usuarios/propietarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    modelo_barco VARCHAR(10) NOT NULL,
    imagen_unidad VARCHAR(500) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Insertar usuarios de ejemplo para diferentes modelos
INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES
('Carlos Pérez', 'carlos.perez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '85', 'assets/img/modelos/klase-85.jpg'),
('María González', 'maria.gonzalez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '64', 'assets/img/modelos/klase-64.jpg'),
('Roberto Silva', 'roberto.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '52', 'assets/img/modelos/klase-52.jpg'),
('Ana Martínez', 'ana.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '43', 'assets/img/modelos/klase-43.jpg'),
('Luis Fernández', 'luis.fernandez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '42', 'assets/img/modelos/klase-42.jpg'),
('Patricia López', 'patricia.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '37', 'assets/img/modelos/klase-37.jpg'),
('Diego Rodríguez', 'diego.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '34', 'assets/img/modelos/klase-34.jpg');

-- Nota: Las contraseñas de ejemplo son "password" encriptadas con password_hash()
-- En producción, cambiar todas las contraseñas por unas seguras