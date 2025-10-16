<?php
require_once 'php/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, nombre_completo, email, password FROM usuarios WHERE email = ? AND activo = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre_completo'];
                $_SESSION['user_email'] = $user['email'];
                
                // Actualizar último acceso
                $stmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header('Location: panel.php');
                exit();
            } else {
                $error = 'Credenciales incorrectas.';
            }
        } catch (Exception $e) {
            $error = 'Error del sistema. Intente nuevamente.';
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Klase A — Acceso al Panel Operativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
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
            border-radius: 8px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section h1 {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: 2px;
        }

        .logo-section p {
            color: var(--text-secondary);
            font-style: italic;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .panel-title {
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-secondary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: transparent;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            color: var(--text-primary);
            font-family: var(--font-body);
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .form-input::placeholder {
            color: var(--text-secondary);
        }

        .login-btn {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background-color: var(--accent-color);
            color: var(--bg-dark);
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background-color: #e0e0e0;
            transform: translateY(-1px);
        }

        .error-message {
            background-color: #7f1d1d;
            border: 1px solid #dc2626;
            color: #fecaca;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 1.5rem 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <h1>KLASE A</h1>
                <p>Marcando tendencia.</p>
                <div class="divider"></div>
                <p class="panel-title">Acceso al Panel Operativo</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="su.email@ejemplo.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Acceder al Panel
                </button>
            </form>

            <div class="footer-text">
                <p>&copy; 2025 Astillero Klase A.</p>
                <p style="margin-top: 0.5rem; font-size: 0.7rem;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Usuario demo: cualquier email | Contraseña: password123
                </p>
            </div>
        </div>
    </div>
</body>
</html>