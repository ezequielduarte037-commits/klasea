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