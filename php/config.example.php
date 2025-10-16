<?php
/**
 * Archivo de configuración EJEMPLO - Klase A Panel de Propietarios
 * 
 * INSTRUCCIONES:
 * 1. Copiar este archivo como "config.php"
 * 2. Completar con tus credenciales reales
 * 3. NO subir config.php a Git (está en .gitignore)
 */

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================

define('DB_HOST', 'localhost');              // Host de MySQL (ej: localhost, 127.0.0.1)
define('DB_NAME', 'klasea_clients');         // Nombre de la base de datos
define('DB_USER', 'root');                   // Usuario de MySQL
define('DB_PASS', '');                       // Contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');             // Charset (no cambiar)

// =====================================================
// CONFIGURACIÓN DEL SISTEMA
// =====================================================

// URL del sitio (sin barra final)
// Desarrollo: http://localhost
// Producción: https://panel.klasea.com
define('SITE_URL', 'http://localhost');

// Duración de la sesión en segundos
// 3600 = 1 hora
// 7200 = 2 horas
// 86400 = 24 horas
define('SESSION_LIFETIME', 3600);

// =====================================================
// CONFIGURACIÓN REGIONAL
// =====================================================

// Zona horaria
// Opciones: https://www.php.net/manual/es/timezones.php
// Argentina: America/Argentina/Buenos_Aires
// España: Europe/Madrid
// México: America/Mexico_City
date_default_timezone_set('America/Argentina/Buenos_Aires');

// =====================================================
// INICIALIZACIÓN
// =====================================================

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// FUNCIONES DEL SISTEMA
// =====================================================

/**
 * Obtener conexión a base de datos
 * @return PDO
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die("Error de conexión a la base de datos. Contacte al administrador.");
    }
}

/**
 * Verificar sesión activa
 */
function checkSession() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        header('Location: /login.php');
        exit;
    }
    
    // Verificar tiempo de sesión
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        header('Location: /login.php?timeout=1');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Obtener saludo según hora del día
 * @return string
 */
function getSaludo() {
    $hora = (int)date('H');
    if ($hora >= 6 && $hora < 12) {
        return 'Buenos días';
    } elseif ($hora >= 12 && $hora < 20) {
        return 'Buenas tardes';
    } else {
        return 'Buenas noches';
    }
}
?>
