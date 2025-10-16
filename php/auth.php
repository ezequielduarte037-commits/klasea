<?php
/**
 * Sistema de autenticación - Klase A
 * Maneja login, logout y verificación de usuarios
 */

require_once 'config.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Procesar login de usuario
     */
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nombre'] = $user['nombre_completo'];
                $_SESSION['user_modelo'] = $user['modelo_barco'];
                $_SESSION['user_imagen'] = $user['imagen_unidad'];
                $_SESSION['last_activity'] = time();
                
                // Actualizar último acceso
                $this->updateLastAccess($user['id']);
                
                // Registrar sesión
                $this->logSession($user['id']);
                
                return [
                    'success' => true,
                    'redirect' => '/panel.php'
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Credenciales incorrectas'
            ];
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error del sistema. Intente nuevamente.'
            ];
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /login.php');
        exit;
    }
    
    /**
     * Actualizar último acceso del usuario
     */
    private function updateLastAccess($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Error actualizando último acceso: " . $e->getMessage());
        }
    }
    
    /**
     * Registrar sesión en base de datos
     */
    private function logSession($userId) {
        try {
            $stmt = $this->db->prepare("INSERT INTO sesiones (usuario_id, ip_address, user_agent) VALUES (?, ?, ?)");
            $stmt->execute([
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando sesión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener datos del usuario actual
     */
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error obteniendo usuario: " . $e->getMessage());
            return null;
        }
    }
}
?>
