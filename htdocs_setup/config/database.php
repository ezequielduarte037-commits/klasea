<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'panel_operaciones');
define('DB_USER', 'root');
define('DB_PASS', '');

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Función para verificar si la base de datos existe
function checkDatabase() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SHOW DATABASES LIKE ?");
        $stmt->execute([DB_NAME]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}
?>