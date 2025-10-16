<?php
require_once '../php/config.php';

// Verificar si es administrador
$admin_email = 'admin@klasea.com';
if (!isLoggedIn() || $_SESSION['user_email'] !== $admin_email) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $imagen = trim($_POST['imagen'] ?? '');
    
    if (empty($id) || empty($nombre) || empty($email) || empty($modelo)) {
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
        
        // Verificar si el email ya existe en otro usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El email ya está en uso']);
            exit;
        }
        
        // Actualizar usuario
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, password = ?, modelo_barco = ?, imagen_unidad = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $hashedPassword, $modelo, $imagen, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, email = ?, modelo_barco = ?, imagen_unidad = ? WHERE id = ?");
            $stmt->execute([$nombre, $email, $modelo, $imagen, $id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        
    } catch (PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>