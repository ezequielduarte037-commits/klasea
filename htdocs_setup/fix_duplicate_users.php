<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Corrección de Usuarios Duplicados</h2>";

try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    
    echo "<p style='color: green;'>✓ Conexión a base de datos exitosa</p>";
    
    // Verificar usuarios existentes
    $stmt = $pdo->query("SELECT id, username, email, nombre, rol, activo FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Usuarios existentes en la base de datos:</h3>";
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
    
    // Verificar si existe usuario admin
    $stmt = $pdo->prepare("SELECT id, username, password FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p style='color: blue;'>ℹ Usuario admin encontrado. ID: " . $admin['id'] . "</p>";
        
        // Verificar si la contraseña funciona
        if (password_verify('admin123', $admin['password'])) {
            echo "<p style='color: green;'>✓ La contraseña del admin funciona correctamente</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Actualizando contraseña del admin...</p>";
            
            // Actualizar contraseña
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
            $stmt->execute([$new_hash]);
            
            echo "<p style='color: green;'>✓ Contraseña del admin actualizada</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Usuario admin no encontrado. Creando...</p>";
        
        // Crear usuario admin
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, email, nombre, rol, activo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', $password_hash, 'admin@panel.com', 'Administrador', 'admin', 1]);
        
        echo "<p style='color: green;'>✓ Usuario admin creado correctamente</p>";
    }
    
    // Limpiar usuarios duplicados (opcional)
    echo "<h3>Limpieza de datos (opcional):</h3>";
    echo "<p>Si quieres limpiar la base de datos y empezar de cero, puedes:</p>";
    echo "<a href='?action=clean' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Limpiar Base de Datos</a>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'clean') {
        echo "<p style='color: orange;'>⚠ Limpiando base de datos...</p>";
        
        // Eliminar todos los usuarios excepto admin
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE username != 'admin'");
        $stmt->execute();
        
        // Eliminar operaciones
        $stmt = $pdo->prepare("DELETE FROM operaciones");
        $stmt->execute();
        
        // Eliminar métricas
        $stmt = $pdo->prepare("DELETE FROM metricas");
        $stmt->execute();
        
        // Eliminar sesiones
        $stmt = $pdo->prepare("DELETE FROM sesiones");
        $stmt->execute();
        
        // Insertar datos de ejemplo
        $stmt = $pdo->prepare("
            INSERT INTO operaciones (titulo, descripcion, tipo, estado, prioridad, fecha_vencimiento) VALUES 
            ('Revisión de artículo sobre tecnología', 'Revisar y corregir artículo sobre las últimas tendencias tecnológicas', 'revision', 'pendiente', 'alta', '2024-02-15'),
            ('Diseño de portada revista', 'Crear diseño para la portada de la revista de marzo', 'diseno', 'en_proceso', 'media', '2024-02-20'),
            ('Edición de entrevista', 'Editar entrevista con el CEO de la empresa', 'edicion', 'pendiente', 'baja', '2024-02-25'),
            ('Publicación de noticias', 'Publicar noticias del sector editorial', 'publicacion', 'completado', 'media', '2024-02-10')
        ");
        $stmt->execute();
        
        $stmt = $pdo->prepare("
            INSERT INTO metricas (fecha, tipo, valor, descripcion) VALUES 
            ('2024-02-01', 'operaciones_completadas', 15, 'Operaciones completadas en febrero'),
            ('2024-02-01', 'operaciones_pendientes', 8, 'Operaciones pendientes'),
            ('2024-02-01', 'tiempo_promedio', 2.5, 'Tiempo promedio en días'),
            ('2024-02-01', 'eficiencia', 85.5, 'Porcentaje de eficiencia')
        ");
        $stmt->execute();
        
        echo "<p style='color: green;'>✓ Base de datos limpiada y datos de ejemplo insertados</p>";
    }
    
    echo "<h3>Estado final:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de usuarios: " . $total['total'] . "</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM operaciones");
    $total = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total de operaciones: " . $total['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Duplicate Users - Panel Operativo Editorial</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }
        
        table {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            overflow: hidden;
            margin: 10px 0;
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
            margin: 10px 5px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn:hover {
            background: #f0f0f0;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-3xl font-bold mb-6">🔧 Corrección de Usuarios Duplicados</h1>
        
        <div class="mb-6">
            <a href="login_simple.php" class="btn">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Ir al Login
            </a>
            <a href="?action=clean" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i>
                Limpiar Base de Datos
            </a>
        </div>
    </div>
</body>
</html>