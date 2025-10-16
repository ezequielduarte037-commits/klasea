<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';

// Verificar si ya está logueado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            // Intentar conectar a la base de datos
            require_once 'config/database.php';
            $pdo = getDBConnection();
            
            // Buscar usuario (con verificación simple de contraseña)
            $stmt = $pdo->prepare("SELECT id, username, password, nombre, rol, activo FROM usuarios WHERE username = ? AND activo = 1");
            $stmt->execute([$username]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Verificar contraseña (tanto con hash como texto plano para compatibilidad)
                $password_valid = false;
                
                // Intentar verificación con hash
                if (password_verify($password, $usuario['password'])) {
                    $password_valid = true;
                }
                // Si no funciona, verificar como texto plano (para desarrollo)
                elseif ($usuario['password'] === $password) {
                    $password_valid = true;
                }
                // Verificar contraseña por defecto
                elseif ($password === 'admin123' && $username === 'admin') {
                    $password_valid = true;
                }
                
                if ($password_valid) {
                    // Login exitoso
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['username'] = $usuario['username'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['rol'] = $usuario['rol'];
                    
                    // Actualizar último acceso
                    try {
                        $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                        $stmt->execute([$usuario['id']]);
                    } catch (Exception $e) {
                        // Error silencioso
                    }
                    
                    header('Location: index.html');
                    exit();
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado.';
            }
        } catch (Exception $e) {
            $error = 'Error del sistema: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Operativo Editorial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="login-container rounded-2xl p-8 w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2" style="font-family: 'Montserrat', sans-serif;">
                Panel Operativo
            </h1>
            <p class="text-gray-400">Editorial</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                    <i class="fas fa-user mr-2"></i>Usuario
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       required
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                       placeholder="Ingresa tu usuario"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? 'admin'); ?>">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    <i class="fas fa-lock mr-2"></i>Contraseña
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                       placeholder="Ingresa tu contraseña"
                       value="admin123">
            </div>
            
            <button type="submit" 
                    class="w-full bg-white text-black py-3 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-8 text-center text-sm text-gray-400">
            <p>Credenciales por defecto:</p>
            <p><strong>Usuario:</strong> admin</p>
            <p><strong>Contraseña:</strong> admin123</p>
        </div>
        
        <div class="mt-4 text-center">
            <a href="login_debug.php" class="text-blue-400 hover:text-blue-300 text-sm">
                <i class="fas fa-bug mr-1"></i>
                Ver Debug del Sistema
            </a>
        </div>
    </div>
</body>
</html>