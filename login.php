<?php
require_once __DIR__ . '/php/config.php';
if (is_logged_in()) {
    header('Location: /panel.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso — Klase A</title>
  <link rel="icon" href="/img/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root { --bg:#0b0b0c; --panel:#111214; --text:#e6e6e6; --muted:#9aa0a6; --accent:#c6f432; }
    body{background:var(--bg); color:var(--text); font-family: 'Roboto', system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji';}
    h1,h2,h3{font-family:'Montserrat',sans-serif}
    .card{background:var(--panel); border:1px solid #1f2124; box-shadow: 0 10px 30px rgba(0,0,0,.4)}
    .btn{background:#222428; color:#fff; border:1px solid #2b2e33}
    .btn:hover{border-color:var(--accent); color:var(--accent)}
    input{background:#0f1012; border:1px solid #23262b; color:#fff}
    input:focus{outline:none; border-color:var(--accent)}
    a{color:var(--muted)} a:hover{color:#fff}
  </style>
</head>
<body class="min-h-screen flex items-center justify-center py-16">
  <div class="w-full max-w-md card rounded-xl p-8">
    <div class="mb-6">
      <p class="text-sm text-gray-400 italic">Marcando tendencia.</p>
      <h1 class="text-2xl font-bold">Panel K42</h1>
    </div>
    <?php if ($error === 'invalid'): ?>
      <div class="mb-4 text-sm text-red-400">Credenciales inválidas.</div>
    <?php elseif ($error === 'empty'): ?>
      <div class="mb-4 text-sm text-red-400">Complete email y contraseña.</div>
    <?php endif; ?>
    <form action="/php/auth.php" method="post" class="space-y-4">
      <input type="hidden" name="action" value="login">
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input class="w-full px-4 py-3 rounded-lg" type="email" name="email" required autocomplete="username">
      </div>
      <div>
        <label class="block text-sm mb-1">Contraseña</label>
        <input class="w-full px-4 py-3 rounded-lg" type="password" name="password" required autocomplete="current-password">
      </div>
      <button class="btn w-full px-4 py-3 rounded-lg font-semibold" type="submit">Ingresar</button>
    </form>
    <div class="mt-6 text-xs text-gray-500">© <?=date('Y')?> Astillero Klase A.</div>
  </div>
</body>
</html>
