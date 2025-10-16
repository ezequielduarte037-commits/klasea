<?php
// Verificación de autenticación
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Verificar rol de administrador
function verificarAdmin() {
    verificarAutenticacion();
    if ($_SESSION['rol'] !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        die('Acceso denegado. Se requieren permisos de administrador.');
    }
}

// Obtener datos del usuario actual
function obtenerUsuarioActual() {
    if (!isset($_SESSION['usuario_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'username' => $_SESSION['username'],
        'nombre' => $_SESSION['nombre'],
        'rol' => $_SESSION['rol']
    ];
}

// Cerrar sesión
function cerrarSesion() {
    session_start();
    
    // Eliminar token de sesión de la base de datos
    if (isset($_SESSION['usuario_id'])) {
        try {
            require_once __DIR__ . '/../config/database.php';
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("UPDATE sesiones SET activa = 0 WHERE usuario_id = ? AND activa = 1");
            $stmt->execute([$_SESSION['usuario_id']]);
        } catch (Exception $e) {
            // Error silencioso
        }
    }
    
    // Destruir sesión
    session_destroy();
    header('Location: login.php');
    exit();
}

// Verificar token de sesión
function verificarTokenSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        return false;
    }
    
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id FROM sesiones WHERE usuario_id = ? AND activa = 1 AND fecha_expiracion > NOW() ORDER BY fecha_creacion DESC LIMIT 1");
        $stmt->execute([$_SESSION['usuario_id']]);
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}
?>