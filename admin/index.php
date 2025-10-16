<?php
require_once '../php/config.php';

// Autenticación simple para admin
session_start();

$error = '';
$success = '';

// Login de admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM administradores WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_usuario'] = $admin['usuario'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klase A - Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .admin-box {
            background: #000;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
        }
        
        .input-field {
            background: #222;
            border: 1px solid #333;
            color: #fff;
            padding: 0.75rem;
            border-radius: 4px;
            width: 100%;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #fff;
        }
        
        .btn-primary {
            background: #fff;
            color: #000;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 4px;
            width: 100%;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary:hover {
            background: #ccc;
        }
    </style>
</head>
<body>
    <div class="admin-box">
        <h1 class="text-2xl font-bold text-white mb-6 text-center">
            <i class="fas fa-shield-halved mr-2"></i>
            Administración
        </h1>
        
        <?php if ($error): ?>
            <div class="bg-red-900 border border-red-600 text-red-100 p-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-2">Usuario</label>
                <input type="text" name="usuario" class="input-field" required autofocus>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-2">Contraseña</label>
                <input type="password" name="password" class="input-field" required>
            </div>
            
            <button type="submit" name="login" class="btn-primary">
                Ingresar
            </button>
        </form>
        
        <div class="text-center text-gray-500 text-xs mt-6">
            <p>© 2025 Klase A - Panel Administrativo</p>
        </div>
    </div>
</body>
</html>
