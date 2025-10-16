<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($nombre) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Nombre y email son requeridos']);
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email inválido']);
        exit;
    }
    
    try {
        $pdo = getDBConnection();
        
        // Verificar si el email ya existe en otro usuario
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El email ya está en uso']);
            exit;
        }
        
        // Actualizar datos
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?");
        $stmt->execute([$nombre, $email, $_SESSION['user_id']]);
        
        // Actualizar sesión
        $_SESSION['user_name'] = $nombre;
        $_SESSION['user_email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente']);
        
    } catch (PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>