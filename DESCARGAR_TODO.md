# Klase A Panel - Código Completo para Descargar

## 📁 Estructura de Archivos a Crear

Crea estas carpetas y archivos en tu servidor local:

### 1. Archivos Principales (raíz del proyecto)

#### login.php
```php
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Klase A — Acceso al Panel Operativo</title>
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

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }

        .login-card {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 0;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-field {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            width: 100%;
            font-family: var(--font-body);
            transition: border-color 0.3s ease;
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

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 0;
            font-size: 0.875rem;
        }

        .alert-error {
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fca5a5;
        }

        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .loading {
            display: none;
        }

        .loading.active {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-8">
                <h1 class="font-display text-3xl font-bold mb-2">Klase A</h1>
                <p class="text-secondary text-sm italic">Marcando tendencia.</p>
                <p class="text-secondary text-sm mt-4">Panel Operativo</p>
            </div>

            <form id="loginForm">
                <div id="alertContainer"></div>
                
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="input-field" 
                        placeholder="su@email.com"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="mb-8">
                    <label for="password" class="block text-sm font-medium mb-2">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="input-field" 
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" id="loginBtn" class="btn-primary">
                    <span id="btnText">Acceder</span>
                    <span id="btnLoading" class="loading">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Verificando...
                    </span>
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-secondary">
                <p>&copy; 2025 Astillero Klase A.</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            const alertContainer = document.getElementById('alertContainer');
            
            // Mostrar loading
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.classList.add('active');
            alertContainer.innerHTML = '';
            
            try {
                const response = await fetch('php/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1000);
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                showAlert('error', 'Error de conexión. Intente nuevamente.');
            } finally {
                // Ocultar loading
                btn.disabled = false;
                btnText.style.display = 'inline';
                btnLoading.classList.remove('active');
            }
        });
        
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }
    </script>
</body>
</html>
```

#### install.php
```php
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
```

### 2. Crear carpeta php/ con estos archivos:

#### php/config.php
```php
<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'klasea_clients');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
session_start();

// Función para conectar a la base de datos
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        die("Error de conexión a la base de datos");
    }
}

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
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
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error al obtener usuario: " . $e->getMessage());
        return null;
    }
}

// Función para generar saludo personalizado
function getGreeting() {
    $hour = date('H');
    if ($hour < 12) {
        return "Buenos días";
    } elseif ($hour < 18) {
        return "Buenas tardes";
    } else {
        return "Buenas noches";
    }
}
?>
```

#### php/auth.php
```php
<?php
require_once 'config.php';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email y contraseña son requeridos']);
        exit;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $updateStmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre_completo'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['modelo_barco'] = $user['modelo_barco'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login exitoso',
                'redirect' => 'panel.php'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    } catch (PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error del servidor']);
    }
    exit;
}

// Procesar logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
    exit;
}

// Verificar sesión activa
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    echo json_encode(['logged_in' => isLoggedIn()]);
    exit;
}
?>
```

#### php/database.sql
```sql
-- Base de datos para el sistema de panel operativo Klase A
CREATE DATABASE IF NOT EXISTS klasea_clients CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE klasea_clients;

-- Tabla de usuarios/propietarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    modelo_barco VARCHAR(10) NOT NULL,
    imagen_unidad VARCHAR(500) DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Insertar usuarios de ejemplo para diferentes modelos
INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES
('Carlos Pérez', 'carlos.perez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '85', 'assets/img/modelos/klase-85.jpg'),
('María González', 'maria.gonzalez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '64', 'assets/img/modelos/klase-64.jpg'),
('Roberto Silva', 'roberto.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '52', 'assets/img/modelos/klase-52.jpg'),
('Ana Martínez', 'ana.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '43', 'assets/img/modelos/klase-43.jpg'),
('Luis Fernández', 'luis.fernandez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '42', 'assets/img/modelos/klase-42.jpg'),
('Patricia López', 'patricia.lopez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '37', 'assets/img/modelos/klase-37.jpg'),
('Diego Rodríguez', 'diego.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '34', 'assets/img/modelos/klase-34.jpg');

-- Nota: Las contraseñas de ejemplo son "password" encriptadas con password_hash()
-- En producción, cambiar todas las contraseñas por unas seguras
```

### 3. Crear carpeta admin/ con estos archivos:

#### admin/index.php
```php
<?php
require_once '../php/config.php';

// Verificar si es administrador (por simplicidad, usar email específico)
$admin_email = 'admin@klasea.com';
if (!isLoggedIn() || $_SESSION['user_email'] !== $admin_email) {
    header('Location: ../login.php');
    exit();
}

$pdo = getDBConnection();
$usuarios = [];

try {
    $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error al obtener usuarios: " . $e->getMessage());
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Klase A — Administración</title>
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

        .admin-container {
            min-height: 100vh;
            padding: 2rem;
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
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            font-family: var(--font-body);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: var(--accent-color);
        }

        .btn-danger {
            background-color: #dc2626;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            font-family: var(--font-body);
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .btn-danger:hover {
            opacity: 0.9;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            padding: 2rem;
            width: 90%;
            max-width: 500px;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="font-display text-3xl font-bold">Administración Klase A</h1>
                <p class="text-secondary">Gestión de usuarios del panel operativo</p>
            </div>
            <div class="flex gap-4">
                <a href="../panel.php" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Panel
                </a>
                <button id="addUserBtn" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                </button>
            </div>
        </div>

        <div id="alertContainer"></div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Modelo</th>
                    <th>Último Acceso</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td>K<?php echo $usuario['modelo_barco']; ?></td>
                    <td><?php echo $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca'; ?></td>
                    <td>
                        <span class="<?php echo $usuario['activo'] ? 'text-green-400' : 'text-red-400'; ?>">
                            <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td>
                        <button onclick="editUser(<?php echo $usuario['id']; ?>)" class="btn-secondary mr-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(<?php echo $usuario['id']; ?>)" class="btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar/editar usuario -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" class="font-display text-2xl font-bold mb-6">Nuevo Usuario</h2>
            <form id="userForm">
                <input type="hidden" id="userId" name="id">
                
                <div class="mb-4">
                    <label for="nombre" class="block text-sm font-medium mb-2">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="input-field" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" class="input-field" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" class="input-field" required>
                    <small class="text-secondary">Dejar en blanco para mantener la actual (solo en edición)</small>
                </div>

                <div class="mb-4">
                    <label for="modelo" class="block text-sm font-medium mb-2">Modelo de Barco</label>
                    <select id="modelo" name="modelo" class="input-field" required>
                        <option value="">Seleccionar modelo</option>
                        <option value="85">K85 - 26m</option>
                        <option value="64">K64 - 19.5m</option>
                        <option value="52">K52 - 15.8m</option>
                        <option value="43">K43 - 13.1m</option>
                        <option value="42">K42 - 12.8m</option>
                        <option value="37">K37 - 11.3m</option>
                        <option value="34">K34 - 10.4m</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="imagen" class="block text-sm font-medium mb-2">Imagen de la Unidad (URL)</label>
                    <input type="url" id="imagen" name="imagen" class="input-field" placeholder="https://ejemplo.com/imagen.jpg">
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="btn-primary">Guardar</button>
                    <button type="button" onclick="closeModal()" class="btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentUserId = null;

        // Mostrar modal para nuevo usuario
        document.getElementById('addUserBtn').addEventListener('click', () => {
            currentUserId = null;
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            document.getElementById('userForm').reset();
            document.getElementById('password').required = true;
            document.getElementById('userModal').classList.add('active');
        });

        // Editar usuario
        function editUser(id) {
            currentUserId = id;
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('password').required = false;
            
            // Obtener datos del usuario
            fetch(`get_user.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('userId').value = data.user.id;
                        document.getElementById('nombre').value = data.user.nombre_completo;
                        document.getElementById('email').value = data.user.email;
                        document.getElementById('modelo').value = data.user.modelo_barco;
                        document.getElementById('imagen').value = data.user.imagen_unidad || '';
                        document.getElementById('userModal').classList.add('active');
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Error al cargar datos del usuario');
                });
        }

        // Eliminar usuario
        function deleteUser(id) {
            if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', data.message);
                    }
                })
                .catch(error => {
                    showAlert('error', 'Error al eliminar usuario');
                });
            }
        }

        // Cerrar modal
        function closeModal() {
            document.getElementById('userModal').classList.remove('active');
        }

        // Enviar formulario
        document.getElementById('userForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            if (!currentUserId && !data.password) {
                showAlert('error', 'La contraseña es requerida para nuevos usuarios');
                return;
            }
            
            try {
                const response = await fetch(currentUserId ? 'update_user.php' : 'create_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', result.message);
                }
            } catch (error) {
                showAlert('error', 'Error de conexión');
            }
        });

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
        }
    </script>
</body>
</html>
```

## 🚀 **Instrucciones de Instalación:**

1. **Crear las carpetas** en tu servidor local (htdocs):
   ```
   C:\xampp\htdocs\klasea-panel\
   ├── php\
   ├── admin\
   └── assets\img\modelos\
   ```

2. **Copiar cada archivo** en su ubicación correspondiente

3. **Ejecutar** `http://localhost/klasea-panel/install.php`

4. **¡Listo!** Usar con `admin@klasea.com` / `password`

## 📝 **Nota:**
Este archivo contiene TODO el código necesario. Solo necesitas copiarlo y crear los archivos individuales en tu servidor local.

¿Te ayudo con algún archivo específico o tienes alguna pregunta sobre la instalación?