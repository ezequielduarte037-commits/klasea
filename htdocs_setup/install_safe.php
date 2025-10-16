<?php
// Script de instalación segura que maneja duplicados
error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Configuración de la base de datos
$db_config = [
    'host' => 'localhost',
    'name' => 'panel_operaciones',
    'user' => 'root',
    'pass' => ''
];

if ($_POST) {
    $db_config['host'] = $_POST['db_host'] ?? 'localhost';
    $db_config['name'] = $_POST['db_name'] ?? 'panel_operaciones';
    $db_config['user'] = $_POST['db_user'] ?? 'root';
    $db_config['pass'] = $_POST['db_pass'] ?? '';
    
    try {
        // Probar conexión
        $pdo = new PDO("mysql:host={$db_config['host']};charset=utf8", $db_config['user'], $db_config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Crear base de datos si no existe
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_config['name']}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
        $pdo->exec("USE `{$db_config['name']}`");
        
        // Leer y ejecutar script SQL línea por línea para evitar errores de duplicados
        $sql_file = 'database/panel_operaciones.sql';
        if (file_exists($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            $sql_statements = explode(';', $sql_content);
            
            foreach ($sql_statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Ignorar errores de duplicados y continuar
                        if (strpos($e->getMessage(), 'Duplicate entry') === false && 
                            strpos($e->getMessage(), 'already exists') === false) {
                            throw $e;
                        }
                    }
                }
            }
        }
        
        // Verificar/crear usuario admin
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = 'admin'");
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // Crear usuario admin si no existe
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO usuarios (username, password, email, nombre, rol, activo) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(['admin', $password_hash, 'admin@panel.com', 'Administrador', 'admin', 1]);
        } else {
            // Actualizar contraseña del admin existente
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE username = 'admin'");
            $stmt->execute([$password_hash]);
        }
        
        // Actualizar archivo de configuración
        $config_content = "<?php
// Configuración de la base de datos
define('DB_HOST', '{$db_config['host']}');
define('DB_NAME', '{$db_config['name']}');
define('DB_USER', '{$db_config['user']}');
define('DB_PASS', '{$db_config['pass']}');

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8\", DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return \$pdo;
    } catch(PDOException \$e) {
        die(\"Error de conexión: \" . \$e->getMessage());
    }
}

// Función para verificar si la base de datos existe
function checkDatabase() {
    try {
        \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";charset=utf8\", DB_USER, DB_PASS);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        \$stmt = \$pdo->prepare(\"SHOW DATABASES LIKE ?\");
        \$stmt->execute([DB_NAME]);
        return \$stmt->rowCount() > 0;
    } catch(PDOException \$e) {
        return false;
    }
}
?>";
        
        file_put_contents('config/database.php', $config_content);
        
        $success = '¡Instalación completada exitosamente! Puedes acceder al panel con las credenciales: admin / admin123';
        
    } catch (Exception $e) {
        $error = 'Error durante la instalación: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación Segura - Panel Operativo Editorial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            min-height: 100vh;
        }
        
        .install-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="install-container rounded-2xl p-8 w-full max-w-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2" style="font-family: 'Montserrat', sans-serif;">
                <i class="fas fa-shield-alt mr-3"></i>
                Instalación Segura del Panel
            </h1>
            <p class="text-gray-400">Maneja duplicados y errores automáticamente</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-500/20 border border-green-500/50 text-green-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="text-center">
                <a href="login_simple.php" class="bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-block">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Ir al Login
                </a>
            </div>
        <?php else: ?>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="db_host" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-server mr-2"></i>Servidor de Base de Datos
                        </label>
                        <input type="text" 
                               id="db_host" 
                               name="db_host" 
                               required
                               class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                               placeholder="localhost"
                               value="<?php echo htmlspecialchars($db_config['host']); ?>">
                    </div>
                    
                    <div>
                        <label for="db_name" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-database mr-2"></i>Nombre de la Base de Datos
                        </label>
                        <input type="text" 
                               id="db_name" 
                               name="db_name" 
                               required
                               class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                               placeholder="panel_operaciones"
                               value="<?php echo htmlspecialchars($db_config['name']); ?>">
                    </div>
                    
                    <div>
                        <label for="db_user" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2"></i>Usuario de MySQL
                        </label>
                        <input type="text" 
                               id="db_user" 
                               name="db_user" 
                               required
                               class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                               placeholder="root"
                               value="<?php echo htmlspecialchars($db_config['user']); ?>">
                    </div>
                    
                    <div>
                        <label for="db_pass" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2"></i>Contraseña de MySQL
                        </label>
                        <input type="password" 
                               id="db_pass" 
                               name="db_pass"
                               class="w-full px-4 py-3 bg-gray-800/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent"
                               placeholder="Dejar vacío si no hay contraseña"
                               value="<?php echo htmlspecialchars($db_config['pass']); ?>">
                    </div>
                </div>
                
                <div class="bg-blue-500/20 border border-blue-500/50 text-blue-300 px-4 py-3 rounded-lg">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Instalación Segura:</strong> Este script maneja automáticamente los duplicados y errores existentes. 
                    No perderás datos existentes.
                </div>
                
                <button type="submit" 
                        class="w-full bg-white text-black py-3 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Instalar Panel (Modo Seguro)
                </button>
            </form>
        <?php endif; ?>
        
        <div class="mt-8 text-center text-sm text-gray-400">
            <p>Panel Operativo Editorial v1.0.0 - Instalación Segura</p>
        </div>
    </div>
</body>
</html>