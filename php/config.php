<?php
// Database configuration
// Adjust credentials as needed for your environment
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'klasea_clients';
$DB_PORT = getenv('DB_PORT') ?: 3306;

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, (int)$DB_PORT);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Error de conexión a la base de datos: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Session setup
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_name('KLASEA_SESS');
session_start();
// Simple session expiration (2 hours of inactivity)
const SESSION_TTL_SECONDS = 7200;
if (isset($_SESSION['last_active']) && (time() - (int)$_SESSION['last_active'] > SESSION_TTL_SECONDS)) {
    // Expired session
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: /login.php?error=expired');
    exit;
}
// Refresh activity timestamp on each request
$_SESSION['last_active'] = time();

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

function current_user(mysqli $db): ?array {
    if (!is_logged_in()) return null;
    $stmt = $db->prepare('SELECT id, nombre_completo, email, modelo_barco, imagen_unidad FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}
