<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug del Login</h2>";

// Verificar si la base de datos existe
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar si la tabla usuarios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabla 'usuarios' existe</p>";
        
        // Verificar usuarios en la tabla
        $stmt = $pdo->query("SELECT id, username, password, nombre, rol FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Usuarios en la base de datos:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Password Hash</th><th>Nombre</th><th>Rol</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['username'] . "</td>";
            echo "<td>" . substr($user['password'], 0, 20) . "...</td>";
            echo "<td>" . $user['nombre'] . "</td>";
            echo "<td>" . $user['rol'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>✗ Tabla 'usuarios' NO existe</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error de conexión: " . $e->getMessage() . "</p>";
}

// Probar verificación de contraseña
echo "<h3>Prueba de verificación de contraseña:</h3>";
$password_test = 'admin123';
$hash_test = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($password_test, $hash_test)) {
    echo "<p style='color: green;'>✓ La verificación de contraseña funciona correctamente</p>";
} else {
    echo "<p style='color: red;'>✗ Error en la verificación de contraseña</p>";
}

// Mostrar información del POST
if ($_POST) {
    echo "<h3>Datos recibidos del formulario:</h3>";
    echo "<p>Username: " . ($_POST['username'] ?? 'No enviado') . "</p>";
    echo "<p>Password: " . ($_POST['password'] ?? 'No enviado') . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login - Panel Operativo Editorial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            color: white;
        }
        
        .debug-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="debug-container rounded-2xl p-8 w-full max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2" style="font-family: 'Montserrat', sans-serif;">
                <i class="fas fa-bug mr-3"></i>
                Debug del Sistema de Login
            </h1>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg mb-6">
            <h3 class="text-xl font-semibold mb-4">Formulario de Prueba</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                        Usuario
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="admin"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           value="admin123"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white">
                </div>
                
                <button type="submit" 
                        class="w-full bg-white text-black py-3 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Probar Login
                </button>
            </form>
        </div>
        
        <div class="bg-gray-800 p-6 rounded-lg">
            <h3 class="text-xl font-semibold mb-4">Información del Sistema</h3>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>PDO Available:</strong> <?php echo extension_loaded('pdo') ? 'Sí' : 'No'; ?></p>
            <p><strong>PDO MySQL Available:</strong> <?php echo extension_loaded('pdo_mysql') ? 'Sí' : 'No'; ?></p>
            <p><strong>Session Status:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Activa' : 'Inactiva'; ?></p>
        </div>
    </div>
</body>
</html>