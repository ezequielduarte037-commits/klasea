<?php
require_once __DIR__ . '/php/config.php';
// Simple guard: only allow if logged in; for real admin add roles
// Bootstrap mode: if there are no users yet, allow access to create the first one
$countRes = $mysqli->query('SELECT COUNT(*) AS c FROM usuarios');
$row = $countRes ? $countRes->fetch_assoc() : ['c' => 0];
$bootstrap_mode = ((int)$row['c'] === 0);
if (!$bootstrap_mode) {
  require_login();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

function redirect_admin($msg = '') {
  header('Location: /admin.php' . ($msg ? ('?m=' . urlencode($msg)) : ''));
  exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre_completo'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $modelo = $_POST['modelo_barco'] ?? '42';
  $imagen = trim($_POST['imagen_unidad'] ?? '');
  if ($nombre && $email && $pass) {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $nombre, $email, $hash, $modelo, $imagen);
    $stmt->execute();
    redirect_admin('Usuario creado');
  } else redirect_admin('Completar campos');
}

if ($action === 'delete' && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = $mysqli->prepare('DELETE FROM usuarios WHERE id = ? LIMIT 1');
  $stmt->bind_param('i', $id);
  $stmt->execute();
  redirect_admin('Usuario eliminado');
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $nombre = trim($_POST['nombre_completo'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';
  $modelo = $_POST['modelo_barco'] ?? '42';
  $imagen = trim($_POST['imagen_unidad'] ?? '');
  if ($id && $nombre && $email) {
    if ($pass) {
      $hash = password_hash($pass, PASSWORD_DEFAULT);
      $stmt = $mysqli->prepare('UPDATE usuarios SET nombre_completo=?, email=?, password=?, modelo_barco=?, imagen_unidad=? WHERE id=?');
      $stmt->bind_param('sssssi', $nombre, $email, $hash, $modelo, $imagen, $id);
    } else {
      $stmt = $mysqli->prepare('UPDATE usuarios SET nombre_completo=?, email=?, modelo_barco=?, imagen_unidad=? WHERE id=?');
      $stmt->bind_param('ssssi', $nombre, $email, $modelo, $imagen, $id);
    }
    $stmt->execute();
    redirect_admin('Usuario actualizado');
  } else redirect_admin('Datos inválidos');
}

$users = $mysqli->query('SELECT * FROM usuarios ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administración — Klase A</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <style>:root{--bg:#0b0b0c;--panel:#111214;--border:#1f2124;--accent:#c6f432;--muted:#9aa0a6}body{background:var(--bg);color:#fff;font-family:'Roboto',sans-serif}.card{background:var(--panel);border:1px solid var(--border);border-radius:.75rem;padding:1rem}</style>
</head>
<body class="p-8">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-end justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold" style="font-family:Montserrat,sans-serif">Administración de Usuarios</h1>
        <p class="text-sm text-gray-400">Alta, edición y baja.</p>
        <?php if ($bootstrap_mode): ?>
          <p class="text-xs text-yellow-300 mt-1">Modo inicial: no hay usuarios. Cree el primero para activar el login.</p>
        <?php endif; ?>
      </div>
      <?php if (!$bootstrap_mode): ?>
        <a href="/panel.php" class="text-sm text-gray-400 hover:text-white">Volver al panel</a>
      <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <form class="card" method="post" action="/admin.php">
        <input type="hidden" name="action" value="create">
        <h2 class="font-semibold mb-4">Crear Usuario</h2>
        <label class="text-sm">Nombre</label>
        <input class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded mb-2" name="nombre_completo" required>
        <label class="text-sm">Email</label>
        <input class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded mb-2" type="email" name="email" required>
        <label class="text-sm">Contraseña</label>
        <input class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded mb-2" type="password" name="password" required>
        <label class="text-sm">Modelo</label>
        <select class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded mb-2" name="modelo_barco">
          <option>85</option><option>64</option><option>52</option><option>43</option><option selected>42</option><option>37</option><option>34</option>
        </select>
        <label class="text-sm">Imagen (URL)</label>
        <input class="w-full px-3 py-2 bg-black/50 border border-gray-700 rounded mb-4" name="imagen_unidad">
        <button class="px-4 py-2 border border-gray-700 rounded hover:border-lime-300">Guardar</button>
      </form>

      <div class="md:col-span-2 card">
        <h2 class="font-semibold mb-4">Usuarios</h2>
        <table class="w-full text-sm">
          <thead class="text-gray-400"><tr><th class="text-left">ID</th><th class="text-left">Nombre</th><th class="text-left">Email</th><th>Modelo</th><th>Imagen</th><th></th></tr></thead>
          <tbody>
          <?php foreach($users as $u): ?>
            <tr class="border-t border-gray-800">
              <td class="py-2"><?=$u['id']?></td>
              <td><?=$u['nombre_completo']?></td>
              <td><?=$u['email']?></td>
              <td class="text-center"><?=$u['modelo_barco']?></td>
              <td class="truncate max-w-[200px]"><a class="text-gray-400 hover:text-white" href="<?=htmlspecialchars($u['imagen_unidad'])?>" target="_blank"><?=htmlspecialchars($u['imagen_unidad'])?></a></td>
              <td class="text-right">
                <details>
                  <summary class="cursor-pointer text-gray-400 hover:text-white">Editar</summary>
                  <form class="mt-2" method="post" action="/admin.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?=$u['id']?>">
                    <div class="grid grid-cols-6 gap-2">
                      <input class="col-span-2 px-2 py-1 bg-black/50 border border-gray-700 rounded" name="nombre_completo" value="<?=htmlspecialchars($u['nombre_completo'])?>">
                      <input class="col-span-2 px-2 py-1 bg-black/50 border border-gray-700 rounded" name="email" value="<?=htmlspecialchars($u['email'])?>">
                      <input class="col-span-1 px-2 py-1 bg-black/50 border border-gray-700 rounded" placeholder="Nueva clave" type="password" name="password">
                      <select class="col-span-1 px-2 py-1 bg-black/50 border border-gray-700 rounded" name="modelo_barco">
                        <?php foreach(['85','64','52','43','42','37','34'] as $m): ?>
                          <option value="<?=$m?>" <?=$u['modelo_barco']===$m?'selected':''?>><?=$m?></option>
                        <?php endforeach; ?>
                      </select>
                      <input class="col-span-5 px-2 py-1 bg-black/50 border border-gray-700 rounded" name="imagen_unidad" value="<?=htmlspecialchars($u['imagen_unidad'])?>">
                      <button class="col-span-1 px-2 py-1 border border-gray-700 rounded hover:border-lime-300">Guardar</button>
                    </div>
                  </form>
                  <a class="inline-block mt-2 text-red-400" href="/admin.php?action=delete&id=<?=$u['id']?>" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
                </details>
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
