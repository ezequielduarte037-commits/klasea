<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Corrección de Base de Datos</h2>";

try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    
    echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar si existe el usuario admin
    $stmt = $pdo->query("SELECT id, username, password FROM usuarios WHERE username = 'admin'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p>Usuario admin encontrado. ID: " . $admin['id'] . "</p>";
        echo "<p>Password hash actual: " . substr($admin['password'], 0, 30) . "...</p>";
        
        // Crear hash correcto para admin123
        $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
        echo "<p>Nuevo hash generado: " . substr($new_hash, 0, 30) . "...</p>";
        
        // Actualizar la contraseña
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
        $stmt->execute([$new_hash]);
        
        echo "<p style='color: green;'>✓ Contraseña actualizada correctamente</p>";
        
        // Verificar que funciona
        if (password_verify('admin123', $new_hash)) {
            echo "<p style='color: green;'>✓ Verificación de contraseña exitosa</p>";
        } else {
            echo "<p style='color: red;'>✗ Error en verificación de contraseña</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Usuario admin no encontrado</p>";
        
        // Crear usuario admin
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, email, nombre, rol, activo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', $password_hash, 'admin@panel.com', 'Administrador', 'admin', 1]);
        
        echo "<p style='color: green;'>✓ Usuario admin creado correctamente</p>";
    }
    
    // Mostrar todos los usuarios
    echo "<h3>Usuarios en la base de datos:</h3>";
    $stmt = $pdo->query("SELECT id, username, email, nombre, rol, activo FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Nombre</th><th>Rol</th><th>Activo</th></tr>";
    foreach ($usuarios as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['nombre'] . "</td>";
        echo "<td>" . $user['rol'] . "</td>";
        echo "<td>" . ($user['activo'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database - Panel Operativo Editorial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
            color: white;
            padding: 20px;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }
        
        .btn {
            display: inline-block;
            background: white;
            color: black;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-3xl font-bold mb-6">🔧 Corrección de Base de Datos</h1>
        
        <div class="mb-6">
            <a href="login_simple.php" class="btn">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Ir al Login
            </a>
            <a href="install.php" class="btn">
                <i class="fas fa-download mr-2"></i>
                Reinstalar
            </a>
        </div>
    </div>
</body>
</html>