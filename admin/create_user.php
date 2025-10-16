<?php
require_once '../php/config.php';

// Verificar si es administrador
$admin_email = 'admin@klasea.com';
if (!isLoggedIn() || $_SESSION['user_email'] !== $admin_email) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $imagen = trim($_POST['imagen'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($password) || empty($modelo)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos']);
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }
    
    // Validar modelo
    $modelos_validos = ['85', '64', '52', '43', '42', '37', '34'];
    if (!in_array($modelo, $modelos_validos)) {
        echo json_encode(['success' => false, 'message' => 'Modelo inválido']);
        exit;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El email ya está en uso']);
            exit;
        }
        
        // Crear usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $hashedPassword, $modelo, $imagen]);
        
        echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
        
    } catch (PDOException $e) {
        error_log("Error al crear usuario: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>