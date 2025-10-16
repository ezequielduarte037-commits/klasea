<?php
require_once '../php/config.php';

session_start();

// Verificar sesión de admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$db = getDBConnection();
$success = '';
$error = '';

// Agregar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $imagen = $_POST['imagen'] ?? '';
    
    if ($nombre && $email && $password && $modelo) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $hashedPassword, $modelo, $imagen]);
            $success = 'Usuario agregado correctamente';
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Complete todos los campos obligatorios';
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Usuario eliminado correctamente';
    } catch (PDOException $e) {
        $error = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Obtener todos los usuarios
$stmt = $db->query("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administración Klase A</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@300;400&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: #0a0a0a;
            color: #fff;
        }
        
        .header {
            background: #000;
            border-bottom: 1px solid #333;
            padding: 1.5rem;
        }
        
        .card {
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .input-field {
            background: #222;
            border: 1px solid #333;
            color: #fff;
            padding: 0.5rem;
            border-radius: 4px;
            width: 100%;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #fff;
        }
        
        .btn-primary {
            background: #fff;
            color: #000;
            font-weight: 700;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            background: #ccc;
        }
        
        .btn-danger {
            background: #dc2626;
            color: #fff;
            font-weight: 500;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 0.85rem;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #1a1a1a;
            padding: 0.75rem;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #999;
        }
        
        td {
            padding: 0.75rem;
            border-bottom: 1px solid #222;
        }
        
        tr:hover {
            background: #1a1a1a;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-shield-halved mr-2"></i>
                Panel de Administración - Klase A
            </h1>
            <a href="logout.php" class="text-red-400 hover:text-red-300">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
            </a>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto p-6">
        <?php if ($success): ?>
            <div class="bg-green-900 border border-green-600 text-green-100 p-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-900 border border-red-600 text-red-100 p-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="card">
                <h3 class="text-sm text-gray-400 mb-1">Total Usuarios</h3>
                <p class="text-3xl font-bold"><?php echo count($usuarios); ?></p>
            </div>
            <div class="card">
                <h3 class="text-sm text-gray-400 mb-1">Usuarios Activos</h3>
                <p class="text-3xl font-bold"><?php echo count(array_filter($usuarios, fn($u) => $u['activo'])); ?></p>
            </div>
            <div class="card">
                <h3 class="text-sm text-gray-400 mb-1">Modelos Registrados</h3>
                <p class="text-3xl font-bold"><?php echo count(array_unique(array_column($usuarios, 'modelo_barco'))); ?></p>
            </div>
        </div>
        
        <div class="card mb-6">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-user-plus mr-2"></i>Agregar Nuevo Usuario
            </h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Nombre Completo *</label>
                    <input type="text" name="nombre" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Email *</label>
                    <input type="email" name="email" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Contraseña *</label>
                    <input type="password" name="password" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-2">Modelo de Barco *</label>
                    <select name="modelo" class="input-field" required>
                        <option value="">Seleccione...</option>
                        <option value="85">K85</option>
                        <option value="64">K64</option>
                        <option value="52">K52</option>
                        <option value="43">K43</option>
                        <option value="42">K42</option>
                        <option value="37">K37</option>
                        <option value="34">K34</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-400 mb-2">URL Imagen Unidad</label>
                    <input type="url" name="imagen" class="input-field" placeholder="https://...">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="agregar" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Agregar Usuario
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-users mr-2"></i>Lista de Usuarios
            </h2>
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Modelo</th>
                            <th>Fecha Registro</th>
                            <th>Último Acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>K<?php echo $user['modelo_barco']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($user['fecha_registro'])); ?></td>
                                <td><?php echo $user['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($user['ultimo_acceso'])) : '-'; ?></td>
                                <td>
                                    <?php if ($user['activo']): ?>
                                        <span class="text-green-400"><i class="fas fa-circle-check"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="text-gray-500"><i class="fas fa-circle"></i> Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?eliminar=<?php echo $user['id']; ?>" 
                                       class="btn-danger"
                                       onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
