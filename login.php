<?php
require_once 'php/config.php';
require_once 'php/auth.php';

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['user_id'])) {
    header('Location: /panel.php');
    exit;
}

$error = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        $auth = new Auth();
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            header('Location: ' . $result['redirect']);
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

// Verificar si hay timeout
if (isset($_GET['timeout'])) {
    $error = 'Su sesión ha expirado. Por favor ingrese nuevamente.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klase A — Acceso Propietarios</title>
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

        .login-box {
            background-color: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .main-title {
            font-family: var(--font-display);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .input-field {
            width: 100%;
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.85rem 1rem;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .input-field::placeholder {
            color: var(--text-secondary);
        }

        .btn-primary {
            width: 100%;
            background-color: var(--text-primary);
            color: var(--bg-dark);
            padding: 0.9rem;
            border-radius: 4px;
            font-family: var(--font-display);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--text-secondary);
            color: var(--text-primary);
        }

        .error-message {
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fca5a5;
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .tagline {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-style: italic;
            margin-top: 2rem;
        }

        .footer {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.75rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-anchor"></i>
                </div>
                <h1 class="main-title">Panel de Propietarios</h1>
                <p class="subtitle">Klase A</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="input-field" 
                        placeholder="su.email@ejemplo.com"
                        required
                        autofocus
                    >
                </div>

                <div class="input-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="input-field" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-sign-in-alt mr-2"></i>Acceder al Panel
                </button>
            </form>

            <p class="tagline">Marcando tendencia.</p>

            <div class="footer">
                <p>&copy; 2025 Astillero Klase A. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>

    <script>
        // Animación de entrada suave
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.login-box').style.opacity = '0';
            document.querySelector('.login-box').style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                document.querySelector('.login-box').style.transition = 'all 0.5s ease';
                document.querySelector('.login-box').style.opacity = '1';
                document.querySelector('.login-box').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
