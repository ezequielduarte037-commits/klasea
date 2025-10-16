<?php
/**
 * Script de instalación automática para Klase A Panel Operativo
 * Ejecutar una sola vez para configurar el sistema
 */

// Configuración de la base de datos (ajustar según tu entorno)
$db_config = [
    'host' => 'localhost',
    'name' => 'klasea_clients',
    'user' => 'root',
    'pass' => ''
];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Klase A — Instalación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        :root {
            --bg-dark: #000000;
            --text-primary: #FFFFFF;
            --text-secondary: #A0A0A0;
            --border-color: #333333;
            --accent-color: #FFFFFF;
            --font-display: 'Montserrat', sans-serif;
            --font-body: 'Roboto', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-dark);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .install-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .install-card {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
        }

        .input-field {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            width: 100%;
            font-family: var(--font-body);
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .btn-primary {
            background-color: var(--accent-color);
            color: var(--bg-dark);
            border: none;
            padding: 0.75rem 2rem;
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: opacity 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .alert-error {
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fca5a5;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="text-center mb-8">
                <h1 class="font-display text-3xl font-bold mb-2">Klase A</h1>
                <p class="text-secondary text-sm italic">Marcando tendencia.</p>
                <p class="text-secondary text-sm mt-4">Instalación del Panel Operativo</p>
            </div>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step'])) {
                $step = $_POST['step'];
                
                if ($step === '1') {
                    // Paso 1: Verificar requisitos
                    echo '<div class="step active">';
                    echo '<h2 class="font-display text-2xl font-bold mb-6">Verificación de Requisitos</h2>';
                    
                    $requirements = [
                        'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                        'PDO Extension' => extension_loaded('pdo'),
                        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
                        'Session Support' => function_exists('session_start'),
                        'JSON Support' => function_exists('json_encode'),
                        'Password Hash Support' => function_exists('password_hash')
                    ];
                    
                    $all_ok = true;
                    foreach ($requirements as $req => $status) {
                        $class = $status ? 'alert-success' : 'alert-error';
                        $icon = $status ? 'fa-check' : 'fa-times';
                        echo "<div class='alert $class'><i class='fas $icon mr-2'></i>$req</div>";
                        if (!$status) $all_ok = false;
                    }
                    
                    if ($all_ok) {
                        echo '<div class="alert alert-success"><i class="fas fa-check mr-2"></i>Todos los requisitos están cumplidos</div>';
                        echo '<form method="POST" class="mt-6">';
                        echo '<input type="hidden" name="step" value="2">';
                        echo '<button type="submit" class="btn-primary">Continuar</button>';
                        echo '</form>';
                    } else {
                        echo '<div class="alert alert-error"><i class="fas fa-exclamation-triangle mr-2"></i>Por favor, instale los requisitos faltantes antes de continuar</div>';
                    }
                    echo '</div>';
                    
                } elseif ($step === '2') {
                    // Paso 2: Configurar base de datos
                    echo '<div class="step active">';
                    echo '<h2 class="font-display text-2xl font-bold mb-6">Configuración de Base de Datos</h2>';
                    
                    $host = $_POST['db_host'] ?? $db_config['host'];
                    $name = $_POST['db_name'] ?? $db_config['name'];
                    $user = $_POST['db_user'] ?? $db_config['user'];
                    $pass = $_POST['db_pass'] ?? $db_config['pass'];
                    
                    try {
                        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        // Crear base de datos
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        $pdo->exec("USE `$name`");
                        
                        // Ejecutar script SQL
                        $sql = file_get_contents('php/database.sql');
                        $pdo->exec($sql);
                        
                        // Crear archivo de configuración
                        $config_content = "<?php
// Configuración de la base de datos
define('DB_HOST', '$host');
define('DB_NAME', '$name');
define('DB_USER', '$user');
define('DB_PASS', '$pass');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
session_start();

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        \$pdo = new PDO(
            \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return \$pdo;
    } catch (PDOException \$e) {
        error_log(\"Error de conexión a la base de datos: \" . \$e->getMessage());
        die(\"Error de conexión a la base de datos\");
    }
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset(\$_SESSION['user_id']) && !empty(\$_SESSION['user_id']);
}

// Función para redirigir si no está logueado
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Función para obtener datos del usuario actual
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        \$pdo = getDBConnection();
        \$stmt = \$pdo->prepare(\"SELECT * FROM usuarios WHERE id = ?\");
        \$stmt->execute([\$_SESSION['user_id']]);
        return \$stmt->fetch();
    } catch (PDOException \$e) {
        error_log(\"Error al obtener usuario: \" . \$e->getMessage());
        return null;
    }
}

// Función para generar saludo personalizado
function getGreeting() {
    \$hour = date('H');
    if (\$hour < 12) {
        return \"Buenos días\";
    } elseif (\$hour < 18) {
        return \"Buenas tardes\";
    } else {
        return \"Buenas noches\";
    }
}
?>";
                        
                        file_put_contents('php/config.php', $config_content);
                        
                        echo '<div class="alert alert-success"><i class="fas fa-check mr-2"></i>Base de datos creada correctamente</div>';
                        echo '<div class="alert alert-success"><i class="fas fa-check mr-2"></i>Archivo de configuración creado</div>';
                        echo '<div class="alert alert-success"><i class="fas fa-check mr-2"></i>Usuarios de ejemplo creados</div>';
                        
                        echo '<div class="mt-8">';
                        echo '<h3 class="font-bold text-lg mb-4">Usuarios de Prueba Creados:</h3>';
                        echo '<div class="bg-gray-900 p-4 rounded">';
                        echo '<p class="text-sm mb-2"><strong>Administrador:</strong></p>';
                        echo '<p class="text-sm mb-4">Email: admin@klasea.com | Contraseña: password</p>';
                        echo '<p class="text-sm mb-2"><strong>Usuarios de Prueba:</strong></p>';
                        echo '<p class="text-sm">carlos.perez@email.com | maria.gonzalez@email.com | roberto.silva@email.com</p>';
                        echo '<p class="text-sm">ana.martinez@email.com | luis.fernandez@email.com | patricia.lopez@email.com</p>';
                        echo '<p class="text-sm">diego.rodriguez@email.com</p>';
                        echo '<p class="text-sm mt-2">Contraseña para todos: <strong>password</strong></p>';
                        echo '</div>';
                        echo '</div>';
                        
                        echo '<div class="mt-8">';
                        echo '<a href="login.php" class="btn-primary inline-block text-center">Ir al Login</a>';
                        echo '</div>';
                        
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-error"><i class="fas fa-times mr-2"></i>Error de conexión: ' . $e->getMessage() . '</div>';
                        echo '<form method="POST" class="mt-6">';
                        echo '<input type="hidden" name="step" value="2">';
                        echo '<div class="mb-4">';
                        echo '<label class="block text-sm font-medium mb-2">Host de Base de Datos</label>';
                        echo '<input type="text" name="db_host" class="input-field" value="' . htmlspecialchars($host) . '">';
                        echo '</div>';
                        echo '<div class="mb-4">';
                        echo '<label class="block text-sm font-medium mb-2">Nombre de Base de Datos</label>';
                        echo '<input type="text" name="db_name" class="input-field" value="' . htmlspecialchars($name) . '">';
                        echo '</div>';
                        echo '<div class="mb-4">';
                        echo '<label class="block text-sm font-medium mb-2">Usuario</label>';
                        echo '<input type="text" name="db_user" class="input-field" value="' . htmlspecialchars($user) . '">';
                        echo '</div>';
                        echo '<div class="mb-6">';
                        echo '<label class="block text-sm font-medium mb-2">Contraseña</label>';
                        echo '<input type="password" name="db_pass" class="input-field" value="' . htmlspecialchars($pass) . '">';
                        echo '</div>';
                        echo '<button type="submit" class="btn-primary">Reintentar</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
            } else {
                // Paso inicial
                echo '<div class="step active">';
                echo '<h2 class="font-display text-2xl font-bold mb-6">Bienvenido a Klase A</h2>';
                echo '<p class="text-secondary mb-6">Este asistente te ayudará a configurar el panel operativo paso a paso.</p>';
                echo '<div class="mb-6">';
                echo '<h3 class="font-bold text-lg mb-4">¿Qué se instalará?</h3>';
                echo '<ul class="text-secondary space-y-2">';
                echo '<li><i class="fas fa-check mr-2 text-green-400"></i>Sistema de base de datos MySQL</li>';
                echo '<li><i class="fas fa-check mr-2 text-green-400"></i>Sistema de autenticación seguro</li>';
                echo '<li><i class="fas fa-check mr-2 text-green-400"></i>Panel personalizado por modelo de barco</li>';
                echo '<li><i class="fas fa-check mr-2 text-green-400"></i>Sistema de administración</li>';
                echo '<li><i class="fas fa-check mr-2 text-green-400"></i>Usuarios de ejemplo para pruebas</li>';
                echo '</ul>';
                echo '</div>';
                echo '<form method="POST">';
                echo '<input type="hidden" name="step" value="1">';
                echo '<button type="submit" class="btn-primary">Comenzar Instalación</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>

        </div>
    </div>
</body>
</html>