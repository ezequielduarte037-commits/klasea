<?php
require_once 'php/config.php';

// Verificar acceso de administrador (simplificado - en producción usar roles)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        if ($_POST['admin_password'] === 'admin123') {
            $_SESSION['admin'] = true;
        } else {
            $admin_error = 'Contraseña de administrador incorrecta.';
        }
    }
    
    if (!isset($_SESSION['admin'])) {
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
                body { font-family: 'Roboto', sans-serif; background: #000; color: #fff; }
                .admin-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
                .admin-card { background: #000; border: 1px solid #333; border-radius: 8px; padding: 2rem; max-width: 400px; width: 100%; }
                .form-input { width: 100%; padding: 0.75rem; background: transparent; border: 1px solid #333; border-radius: 4px; color: #fff; }
                .admin-btn { background: #fff; color: #000; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; }
            </style>
        </head>
        <body>
            <div class="admin-container">
                <div class="admin-card">
                    <h1 class="text-2xl font-bold mb-6 text-center">Panel de Administración</h1>
                    <?php if (isset($admin_error)): ?>
                        <div class="bg-red-900 border border-red-600 text-red-100 p-3 rounded mb-4">
                            <?php echo htmlspecialchars($admin_error); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block mb-2">Contraseña de Administrador</label>
                            <input type="password" name="admin_password" class="form-input" required>
                        </div>
                        <button type="submit" class="admin-btn">Acceder</button>
                    </form>
                    <p class="text-center text-sm text-gray-400 mt-4">
                        <a href="login.php" class="hover:text-white">← Volver al Login</a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Procesar acciones CRUD
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDBConnection();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $nombre = trim($_POST['nombre_completo']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $modelo = $_POST['modelo_barco'];
                $imagen = trim($_POST['imagen_unidad']);
                
                if (!empty($nombre) && !empty($email) && !empty($password) && !empty($modelo)) {
                    try {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$nombre, $email, $hashedPassword, $modelo, $imagen]);
                        $message = 'Usuario creado exitosamente.';
                    } catch (PDOException $e) {
                        if ($e->getCode() == 23000) {
                            $error = 'El email ya existe.';
                        } else {
                            $error = 'Error al crear usuario.';
                        }
                    }
                } else {
                    $error = 'Complete todos los campos obligatorios.';
                }
                break;
                
            case 'update':
                $id = $_POST['id'];
                $nombre = trim($_POST['nombre_completo']);
                $email = trim($_POST['email']);
                $modelo = $_POST['modelo_barco'];
                $imagen = trim($_POST['imagen_unidad']);
                $activo = isset($_POST['activo']) ? 1 : 0;
                
                try {
                    if (!empty($_POST['password'])) {
                        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo=?, email=?, password=?, modelo_barco=?, imagen_unidad=?, activo=? WHERE id=?");
                        $stmt->execute([$nombre, $email, $hashedPassword, $modelo, $imagen, $activo, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE usuarios SET nombre_completo=?, email=?, modelo_barco=?, imagen_unidad=?, activo=? WHERE id=?");
                        $stmt->execute([$nombre, $email, $modelo, $imagen, $activo, $id]);
                    }
                    $message = 'Usuario actualizado exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al actualizar usuario.';
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
                    $stmt->execute([$id]);
                    $message = 'Usuario eliminado exitosamente.';
                } catch (PDOException $e) {
                    $error = 'Error al eliminar usuario.';
                }
                break;
        }
    }
}

// Obtener todos los usuarios
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY nombre_completo");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Klase A — Administración de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #000;
            color: #fff;
        }
        
        .admin-header {
            background: #000;
            border-bottom: 1px solid #333;
            padding: 1.5rem 0;
        }
        
        .admin-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            background: transparent;
            border: 1px solid #333;
            border-radius: 4px;
            color: #fff;
            margin-bottom: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 0.5rem;
        }
        
        .btn-primary { background: #fff; color: #000; }
        .btn-secondary { background: #333; color: #fff; }
        .btn-danger { background: #dc2626; color: #fff; }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        
        .table th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: bold;
        }
        
        .status-active { color: #10b981; }
        .status-inactive { color: #ef4444; }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
        }
        
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #000;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-content">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Administración de Usuarios - Klase A</h1>
                <div>
                    <a href="panel.php" class="btn btn-secondary">
                        <i class="fas fa-tachometer-alt mr-2"></i>Panel
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-content">
        <?php if ($message): ?>
            <div class="bg-green-900 border border-green-600 text-green-100 p-4 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-900 border border-red-600 text-red-100 p-4 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Usuarios Registrados</h2>
                <button onclick="openCreateModal()" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Modelo</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
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
                            <td>
                                <span class="<?php echo $usuario['activo'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td><?php echo $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca'; ?></td>
                            <td>
                                <button onclick="editUser(<?php echo htmlspecialchars(json_encode($usuario)); ?>)" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteUser(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre_completo']); ?>')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar usuario -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-xl font-bold">Nuevo Usuario</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="userForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="userId">

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block mb-2">Nombre Completo *</label>
                        <input type="text" name="nombre_completo" id="nombreCompleto" class="form-input" required>
                    </div>

                    <div>
                        <label class="block mb-2">Email *</label>
                        <input type="email" name="email" id="email" class="form-input" required>
                    </div>

                    <div>
                        <label class="block mb-2">Contraseña <span id="passwordHint">*</span></label>
                        <input type="password" name="password" id="password" class="form-input">
                    </div>

                    <div>
                        <label class="block mb-2">Modelo de Barco *</label>
                        <select name="modelo_barco" id="modeloBarco" class="form-select" required>
                            <option value="">Seleccionar modelo</option>
                            <option value="85">K85</option>
                            <option value="64">K64</option>
                            <option value="52">K52</option>
                            <option value="43">K43</option>
                            <option value="42">K42</option>
                            <option value="37">K37</option>
                            <option value="34">K34</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2">URL de Imagen de la Unidad</label>
                        <input type="url" name="imagen_unidad" id="imagenUnidad" class="form-input" placeholder="https://...">
                    </div>

                    <div id="activoContainer" style="display: none;">
                        <label class="flex items-center">
                            <input type="checkbox" name="activo" id="activo" class="mr-2">
                            Usuario Activo
                        </label>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary mr-2">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nuevo Usuario';
            document.getElementById('formAction').value = 'create';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordHint').textContent = '*';
            document.getElementById('password').required = true;
            document.getElementById('activoContainer').style.display = 'none';
            document.getElementById('userModal').style.display = 'block';
        }

        function editUser(user) {
            document.getElementById('modalTitle').textContent = 'Editar Usuario';
            document.getElementById('formAction').value = 'update';
            document.getElementById('userId').value = user.id;
            document.getElementById('nombreCompleto').value = user.nombre_completo;
            document.getElementById('email').value = user.email;
            document.getElementById('modeloBarco').value = user.modelo_barco;
            document.getElementById('imagenUnidad').value = user.imagen_unidad || '';
            document.getElementById('activo').checked = user.activo == 1;
            document.getElementById('passwordHint').textContent = '(dejar vacío para no cambiar)';
            document.getElementById('password').required = false;
            document.getElementById('activoContainer').style.display = 'block';
            document.getElementById('userModal').style.display = 'block';
        }

        function deleteUser(id, nombre) {
            if (confirm(`¿Está seguro de eliminar al usuario "${nombre}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>