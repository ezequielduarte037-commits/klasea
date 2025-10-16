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