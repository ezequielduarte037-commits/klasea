<?php
// Archivo de configuración de ejemplo
// Copiar a php/config.php y ajustar los valores

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'klasea_clients');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS

// Email del administrador (cambiar por el email real)
define('ADMIN_EMAIL', 'admin@klasea.com');

// Configuración de la aplicación
define('APP_NAME', 'Klase A Panel Operativo');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost'); // Cambiar por la URL real

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de archivos
define('UPLOAD_MAX_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Configuración de email (para futuras notificaciones)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'noreply@klasea.com');
define('SMTP_FROM_NAME', 'Klase A');
?>