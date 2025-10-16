<?php
require_once 'php/config.php';
require_once 'php/auth.php';

// Verificar sesión
checkSession();

// Obtener datos del usuario
$auth = new Auth();
$usuario = $auth->getCurrentUser();

if (!$usuario) {
    header('Location: /login.php');
    exit;
}

// Obtener saludo personalizado
$saludo = getSaludo();
$nombre = explode(' ', $usuario['nombre_completo'])[0]; // Primer nombre
$apellido = explode(' ', $usuario['nombre_completo'])[1] ?? ''; // Primer apellido

// Imagen por defecto según modelo
$imagenes_modelos = [
    '85' => 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=1200',
    '64' => 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?w=1200',
    '52' => 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?w=1200',
    '43' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200',
    '42' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200',
    '37' => 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?w=1200',
    '34' => 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?w=1200'
];

$imagen_bienvenida = $usuario['imagen_unidad'] ?? $imagenes_modelos[$usuario['modelo_barco']] ?? $imagenes_modelos['42'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Klase A — Panel de Propietario K<?php echo $usuario['modelo_barco']; ?></title>
  <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
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
      margin: 0;
      padding: 0;
    }

    .main-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        min-height: 100vh;
    }

    #side-nav {
        background-color: var(--bg-dark);
        border-right: 1px solid var(--border-color);
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 0;
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.3s ease, border-color 0.3s ease;
        border-left: 3px solid transparent;
        padding-left: 1rem;
        cursor: pointer;
    }
    .nav-item:hover {
        color: var(--text-primary);
    }
    .nav-item.active {
        color: var(--accent-color);
        border-left-color: var(--accent-color);
    }
    .nav-number {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1.5rem;
        width: 40px;
    }
    .nav-title {
        font-family: var(--font-display);
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
    }
    
    main {
        padding: 3rem;
        overflow-y: auto;
        height: 100vh;
    }
    
    .content-section { 
        display: none; 
    }
    .content-section.active { 
        display: block; 
        animation: fadeIn 0.5s ease; 
    }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(10px); } 
        to { opacity: 1; transform: translateY(0); } 
    }

    .section-header h1 {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 700;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .section-header p {
        color: var(--text-secondary);
        font-size: 1.1rem;
        max-width: 80ch;
        line-height: 1.6;
    }

    .card {
        background-color: #111;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1.5rem;
    }
    
    .btn-primary {
        background-color: var(--accent-color);
        color: var(--bg-dark);
        font-weight: 700;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-family: var(--font-display);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }
    .btn-primary:hover {
        opacity: 0.8;
    }
    
    .btn-secondary {
        background-color: transparent;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        font-weight: 500;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        cursor: pointer;
        font-family: var(--font-body);
        font-size: 0.9rem;
    }
    .btn-secondary:hover {
        border-color: var(--accent-color);
        color: var(--accent-color);
    }
    
    .input-field {
        background-color: #222;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        color: var(--text-primary);
        padding: 0.5rem;
        width: 100%;
        font-family: var(--font-body);
    }
    .input-field:focus {
        border-color: var(--accent-color);
        outline: none;
    }

    .logo-container img {
        height: 48px;
        margin-bottom: 8px;
    }

    .user-info {
        background: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .user-info h3 {
        font-family: var(--font-display);
        font-size: 0.9rem;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .user-info p {
        color: var(--text-secondary);
        font-size: 0.8rem;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
        border-top: 1px solid var(--border-color);
        margin-top: auto;
        padding-top: 1.5rem;
    }
    .logout-btn:hover {
        color: #ef4444;
    }

    .welcome-image {
        width: 100%;
        border-radius: 8px;
        margin-top: 2rem;
        opacity: 0.9;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .status-online { background-color: #10b981; }
    .status-offline { background-color: #6b7280; }
  </style>
</head>

<body>
  <div class="main-container">
    <aside id="side-nav">
        <div class="logo-container mb-8">
            <img src="assets/img/logo.png" alt="Klase A Logo" onerror="this.style.display='none'">
            <p class="text-sm text-secondary italic mb-4">Marcando tendencia.</p>
            <h2 class="font-bold uppercase tracking-widest text-sm text-secondary">Panel K<?php echo $usuario['modelo_barco']; ?></h2>
        </div>

        <div class="user-info">
            <h3><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></h3>
            <p><i class="fas fa-ship mr-1"></i> Klase A K<?php echo $usuario['modelo_barco']; ?></p>
            <p class="mt-1"><span class="status-indicator status-online"></span>En línea</p>
        </div>
        
        <nav id="main-nav" class="flex-1">
            <a href="#bienvenida" class="nav-item active" data-section="bienvenida">
                <span class="nav-number">01</span>
                <span class="nav-title">Bienvenida</span>
            </a>
            <a href="#configuracion" class="nav-item" data-section="configuracion">
                <span class="nav-number">02</span>
                <span class="nav-title">Configuración</span>
            </a>
            <a href="#resumen" class="nav-item" data-section="resumen">
                <span class="nav-number">03</span>
                <span class="nav-title">Resumen de Estado</span>
            </a>
            <a href="#energia" class="nav-item" data-section="energia">
                <span class="nav-number">04</span>
                <span class="nav-title">Sistema Eléctrico</span>
            </a>
            <a href="#propulsion" class="nav-item" data-section="propulsion">
                <span class="nav-number">05</span>
                <span class="nav-title">Propulsión</span>
            </a>
            <a href="#sistemas" class="nav-item" data-section="sistemas">
                <span class="nav-number">06</span>
                <span class="nav-title">Sistemas a Bordo</span>
            </a>
            <a href="#seguridad" class="nav-item" data-section="seguridad">
                <span class="nav-number">07</span>
                <span class="nav-title">Seguridad</span>
            </a>
            <a href="#tutoriales" class="nav-item" data-section="tutoriales">
                <span class="nav-number">08</span>
                <span class="nav-title">Tutoriales</span>
            </a>
        </nav>

        <a href="php/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar Sesión</span>
        </a>
        
        <div class="mt-4 text-xs text-secondary">
            <p>&copy; 2025 Astillero Klase A.</p>
        </div>
    </aside>

    <main>
      <!-- SECCIÓN BIENVENIDA -->
      <section id="bienvenida" class="content-section active">
        <div class="section-header">
          <h1>Bienvenido a Bordo</h1>
          <p>
            <strong><?php echo $saludo; ?>, <?php echo htmlspecialchars($apellido ? 'Sr. ' . $apellido : $nombre); ?>.</strong><br><br>
            Este es su panel de propietario digital, una herramienta interactiva diseñada para que tenga el control 
            total de su Klase A K<?php echo $usuario['modelo_barco']; ?>. Desde aquí podrá consultar el estado de los sistemas en tiempo real, 
            seguir procedimientos operativos paso a paso y acceder a guías rápidas de seguridad. Estamos aquí para 
            asegurar que su experiencia de navegación sea siempre excepcional, <strong>marcando tendencia</strong> en cada detalle.
          </p>
        </div>
        <img src="<?php echo htmlspecialchars($imagen_bienvenida); ?>" class="welcome-image" alt="Su Klase A K<?php echo $usuario['modelo_barco']; ?>">
      </section>

      <!-- SECCIÓN CONFIGURACIÓN -->
      <section id="configuracion" class="content-section">
        <div class="section-header">
          <h1>Configuración del Panel</h1>
          <p>Personalice los datos de su embarcación.</p>
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="card">
            <h3 class="font-bold text-lg mb-4">Datos del Propietario</h3>
            <div class="mb-4">
              <label for="nombre" class="block text-sm text-secondary mb-2">Nombre Completo</label>
              <input type="text" id="nombre" class="input-field" value="<?php echo htmlspecialchars($usuario['nombre_completo']); ?>" readonly>
            </div>
            <div class="mb-4">
              <label for="email" class="block text-sm text-secondary mb-2">Email</label>
              <input type="email" id="email" class="input-field" value="<?php echo htmlspecialchars($usuario['email']); ?>" readonly>
            </div>
            <div class="mb-4">
              <label for="modelo" class="block text-sm text-secondary mb-2">Modelo</label>
              <input type="text" id="modelo" class="input-field" value="Klase A K<?php echo $usuario['modelo_barco']; ?>" readonly>
            </div>
          </div>
          
          <div class="card">
            <h3 class="font-bold text-lg mb-4">Actualizar Niveles</h3>
            <div class="mb-4">
              <label for="voltage" class="block text-sm text-secondary mb-2">Voltaje Batería (V)</label>
              <input type="number" id="voltage" class="input-field" step="0.1" value="12.6">
            </div>
            <div class="mb-4">
              <label for="fuel" class="block text-sm text-secondary mb-2">Combustible (Litros)</label>
              <input type="number" id="fuel" class="input-field" value="800">
            </div>
            <div class="mb-4">
              <label for="water" class="block text-sm text-secondary mb-2">Agua Potable (Litros)</label>
              <input type="number" id="water" class="input-field" value="500">
            </div>
            <button class="btn-primary" onclick="guardarNiveles()">
              <i class="fas fa-save mr-2"></i>Guardar Niveles
            </button>
          </div>
        </div>
      </section>

      <!-- SECCIÓN RESUMEN DE ESTADO -->
      <section id="resumen" class="content-section">
        <div class="section-header">
          <h1>Resumen de Estado</h1>
          <p>Vista rápida de los sistemas críticos de su embarcación.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
          <div class="card text-center">
            <h3 class="font-bold uppercase text-secondary mb-4">Batería (V)</h3>
            <canvas id="batteryGauge"></canvas>
          </div>
          <div class="card text-center">
            <h3 class="font-bold uppercase text-secondary mb-4">Combustible (%)</h3>
            <canvas id="fuelGauge"></canvas>
          </div>
          <div class="card text-center">
            <h3 class="font-bold uppercase text-secondary mb-4">Agua (%)</h3>
            <canvas id="waterGauge"></canvas>
          </div>
        </div>
      </section>

      <!-- SECCIÓN SISTEMA ELÉCTRICO -->
      <section id="energia" class="content-section">
        <div class="section-header">
          <h1>Sistema Eléctrico</h1>
          <p>Gestión de cortes generales, tableros 12V y fuentes de energía 220V.</p>
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="card">
            <h3 class="font-bold text-lg mb-4 flex items-center">
              <i class="fas fa-car-battery mr-3 text-2xl text-blue-400"></i>
              Cortes Generales
            </h3>
            <p class="text-secondary mb-4">Al abordar, active los cortes de los sistemas que vaya a utilizar.</p>
            <div class="bg-red-900 border border-red-600 p-4 rounded-lg">
              <h4 class="font-bold flex items-center">
                <i class="fas fa-triangle-exclamation mr-2"></i>ADVERTENCIA CRÍTICA
              </h4>
              <p class="mt-2 text-red-100">Al retirarse de la embarcación, corte <strong>TODAS</strong> las baterías sin excepción para prevenir descargas y garantizar la seguridad.</p>
            </div>
          </div>
          
          <div class="card">
            <h3 class="font-bold text-lg mb-4 flex items-center">
              <i class="fas fa-plug mr-3 text-2xl text-yellow-400"></i>
              Tableros y Circuitos
            </h3>
            <p class="text-secondary mb-4">Información de los principales tableros de distribución eléctrica.</p>
            <div class="space-y-2">
              <div class="flex justify-between items-center p-2 bg-zinc-900 rounded">
                <span>Tablero Principal 12V</span>
                <span class="text-green-400"><i class="fas fa-circle-check"></i> Operativo</span>
              </div>
              <div class="flex justify-between items-center p-2 bg-zinc-900 rounded">
                <span>Circuito 220V Costa</span>
                <span class="text-secondary"><i class="fas fa-circle"></i> Desconectado</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN PROPULSIÓN -->
      <section id="propulsion" class="content-section">
        <div class="section-header">
          <h1>Propulsión</h1>
          <p>Sistema de motores, transmisión y controles de navegación.</p>
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="card">
            <h3 class="font-bold text-lg mb-4 flex items-center">
              <i class="fas fa-cog mr-3 text-2xl text-indigo-400"></i>
              Motores Principales
            </h3>
            <p class="text-secondary mb-4">Estado de los motores de propulsión.</p>
            <div class="space-y-3">
              <div class="p-3 bg-zinc-900 rounded">
                <div class="flex justify-between items-center mb-2">
                  <strong>Motor Estribor</strong>
                  <span class="text-green-400"><i class="fas fa-check-circle"></i> OK</span>
                </div>
                <div class="text-sm text-secondary">
                  Horas: 1,250 | RPM: 0 | Temp: --°C
                </div>
              </div>
              <div class="p-3 bg-zinc-900 rounded">
                <div class="flex justify-between items-center mb-2">
                  <strong>Motor Babor</strong>
                  <span class="text-green-400"><i class="fas fa-check-circle"></i> OK</span>
                </div>
                <div class="text-sm text-secondary">
                  Horas: 1,248 | RPM: 0 | Temp: --°C
                </div>
              </div>
            </div>
          </div>
          
          <div class="card">
            <h3 class="font-bold text-lg mb-4 flex items-center">
              <i class="fas fa-gauge-high mr-3 text-2xl text-purple-400"></i>
              Controles de Navegación
            </h3>
            <p class="text-secondary mb-4">Sistema de control y monitoreo.</p>
            <div class="grid grid-cols-2 gap-3">
              <div class="p-3 bg-zinc-900 rounded text-center">
                <div class="text-2xl font-bold">0</div>
                <div class="text-xs text-secondary mt-1">Nudos</div>
              </div>
              <div class="p-3 bg-zinc-900 rounded text-center">
                <div class="text-2xl font-bold">--</div>
                <div class="text-xs text-secondary mt-1">Rumbo</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN SISTEMAS A BORDO -->
      <section id="sistemas" class="content-section">
        <div class="section-header">
          <h1>Sistemas a Bordo</h1>
          <p>Control de sistemas auxiliares: agua, climatización, iluminación.</p>
        </div>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="card text-center">
            <i class="fas fa-water text-4xl text-blue-400 mb-3"></i>
            <h3 class="font-bold mb-2">Sistema de Agua</h3>
            <p class="text-secondary text-sm mb-3">Agua dulce y sanitario</p>
            <button class="btn-secondary w-full">Ver Detalles</button>
          </div>
          
          <div class="card text-center">
            <i class="fas fa-temperature-half text-4xl text-cyan-400 mb-3"></i>
            <h3 class="font-bold mb-2">Climatización</h3>
            <p class="text-secondary text-sm mb-3">A/C y calefacción</p>
            <button class="btn-secondary w-full">Ver Detalles</button>
          </div>
          
          <div class="card text-center">
            <i class="fas fa-lightbulb text-4xl text-yellow-400 mb-3"></i>
            <h3 class="font-bold mb-2">Iluminación</h3>
            <p class="text-secondary text-sm mb-3">Control de luces</p>
            <button class="btn-secondary w-full">Ver Detalles</button>
          </div>
        </div>
      </section>

      <!-- SECCIÓN SEGURIDAD -->
      <section id="seguridad" class="content-section">
        <div class="section-header">
          <h1>Seguridad</h1>
          <p>Procedimientos de emergencia y equipamiento de seguridad.</p>
        </div>
        <div class="mt-8 space-y-6">
          <div class="card">
            <h3 class="font-bold text-lg mb-4 flex items-center">
              <i class="fas fa-life-ring mr-3 text-2xl text-orange-400"></i>
              Equipamiento de Seguridad
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-center justify-between p-3 bg-zinc-900 rounded">
                <span>Chalecos Salvavidas</span>
                <span class="text-green-400"><i class="fas fa-check"></i> A bordo</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-zinc-900 rounded">
                <span>Balsa Salvavidas</span>
                <span class="text-green-400"><i class="fas fa-check"></i> A bordo</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-zinc-900 rounded">
                <span>Extintores</span>
                <span class="text-green-400"><i class="fas fa-check"></i> Vigentes</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-zinc-900 rounded">
                <span>Bengalas</span>
                <span class="text-yellow-400"><i class="fas fa-clock"></i> Revisar venc.</span>
              </div>
            </div>
          </div>
          
          <div class="card bg-red-950 border-red-800">
            <h3 class="font-bold text-lg mb-4 flex items-center text-red-200">
              <i class="fas fa-triangle-exclamation mr-3 text-2xl"></i>
              Procedimientos de Emergencia
            </h3>
            <p class="text-red-100 mb-3">En caso de emergencia, siga estos pasos:</p>
            <ol class="list-decimal list-inside space-y-2 text-red-100">
              <li>Mantenga la calma y evalúe la situación</li>
              <li>Active el sistema VHF en canal 16</li>
              <li>Lance señal de socorro si es necesario</li>
              <li>Reúna a la tripulación en puntos de encuentro</li>
              <li>Prepare equipamiento de seguridad</li>
            </ol>
          </div>
        </div>
      </section>

      <!-- SECCIÓN TUTORIALES -->
      <section id="tutoriales" class="content-section">
        <div class="section-header">
          <h1>Tutoriales</h1>
          <p>Guías paso a paso para operaciones y mantenimiento.</p>
        </div>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="card cursor-pointer hover:border-white transition-all">
            <div class="flex items-start gap-4">
              <div class="bg-zinc-800 p-3 rounded-lg">
                <i class="fas fa-video text-2xl text-blue-400"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-bold mb-2">Arranque de Motores</h3>
                <p class="text-secondary text-sm mb-3">Procedimiento completo de preparación y arranque.</p>
                <span class="text-xs text-blue-400"><i class="fas fa-clock mr-1"></i>5 min</span>
              </div>
            </div>
          </div>
          
          <div class="card cursor-pointer hover:border-white transition-all">
            <div class="flex items-start gap-4">
              <div class="bg-zinc-800 p-3 rounded-lg">
                <i class="fas fa-book text-2xl text-green-400"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-bold mb-2">Amarre y Fondeo</h3>
                <p class="text-secondary text-sm mb-3">Técnicas de amarre seguro y fondeo correcto.</p>
                <span class="text-xs text-green-400"><i class="fas fa-clock mr-1"></i>8 min</span>
              </div>
            </div>
          </div>
          
          <div class="card cursor-pointer hover:border-white transition-all">
            <div class="flex items-start gap-4">
              <div class="bg-zinc-800 p-3 rounded-lg">
                <i class="fas fa-wrench text-2xl text-orange-400"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-bold mb-2">Mantenimiento Básico</h3>
                <p class="text-secondary text-sm mb-3">Tareas de mantenimiento preventivo esenciales.</p>
                <span class="text-xs text-orange-400"><i class="fas fa-clock mr-1"></i>12 min</span>
              </div>
            </div>
          </div>
          
          <div class="card cursor-pointer hover:border-white transition-all">
            <div class="flex items-start gap-4">
              <div class="bg-zinc-800 p-3 rounded-lg">
                <i class="fas fa-life-ring text-2xl text-red-400"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-bold mb-2">Seguridad a Bordo</h3>
                <p class="text-secondary text-sm mb-3">Protocolo de seguridad y uso de equipos.</p>
                <span class="text-xs text-red-400"><i class="fas fa-clock mr-1"></i>10 min</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Sistema de navegación por tabs
    document.addEventListener('DOMContentLoaded', () => {
      const navItems = document.querySelectorAll('.nav-item');
      const sections = document.querySelectorAll('.content-section');
      
      // Función para mostrar sección
      function showSection(sectionId) {
        // Ocultar todas las secciones
        sections.forEach(section => section.classList.remove('active'));
        
        // Desactivar todos los nav items
        navItems.forEach(item => item.classList.remove('active'));
        
        // Activar sección seleccionada
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
          targetSection.classList.add('active');
        }
        
        // Activar nav item correspondiente
        const activeNavItem = document.querySelector(`[data-section="${sectionId}"]`);
        if (activeNavItem) {
          activeNavItem.classList.add('active');
        }
        
        // Scroll al inicio del main
        document.querySelector('main').scrollTop = 0;
      }
      
      // Event listeners para navegación
      navItems.forEach(item => {
        item.addEventListener('click', (e) => {
          e.preventDefault();
          const sectionId = item.dataset.section;
          showSection(sectionId);
          
          // Actualizar hash en URL sin scroll
          history.pushState(null, null, `#${sectionId}`);
        });
      });
      
      // Manejar hash inicial
      if (window.location.hash) {
        const initialSection = window.location.hash.substring(1);
        showSection(initialSection);
      }
      
      // Inicializar gauges
      initGauges();
    });
    
    // Sistema de gauges (medidores circulares)
    function initGauges() {
      const gaugeConfig = {
        type: 'doughnut',
        options: {
          cutout: '75%',
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: false },
            tooltip: { enabled: false },
            datalabels: {
              color: '#FFFFFF',
              font: {
                size: 24,
                weight: 'bold',
                family: 'Montserrat'
              },
              formatter: (value, context) => {
                return context.chart.data.datasets[0].label;
              }
            }
          }
        }
      };
      
      // Batería
      if (document.getElementById('batteryGauge')) {
        new Chart(document.getElementById('batteryGauge'), {
          ...gaugeConfig,
          data: {
            datasets: [{
              label: '12.6 V',
              data: [12.6, 14.4 - 12.6],
              backgroundColor: ['#10b981', '#1a1a1a'],
              borderWidth: 0
            }]
          }
        });
      }
      
      // Combustible
      if (document.getElementById('fuelGauge')) {
        new Chart(document.getElementById('fuelGauge'), {
          ...gaugeConfig,
          data: {
            datasets: [{
              label: '80%',
              data: [80, 20],
              backgroundColor: ['#3b82f6', '#1a1a1a'],
              borderWidth: 0
            }]
          }
        });
      }
      
      // Agua
      if (document.getElementById('waterGauge')) {
        new Chart(document.getElementById('waterGauge'), {
          ...gaugeConfig,
          data: {
            datasets: [{
              label: '65%',
              data: [65, 35],
              backgroundColor: ['#06b6d4', '#1a1a1a'],
              borderWidth: 0
            }]
          }
        });
      }
    }
    
    // Función para guardar niveles
    function guardarNiveles() {
      const voltage = document.getElementById('voltage').value;
      const fuel = document.getElementById('fuel').value;
      const water = document.getElementById('water').value;
      
      // Aquí se puede agregar la lógica para guardar en base de datos
      console.log('Guardando niveles:', { voltage, fuel, water });
      
      // Mostrar notificación
      alert('Niveles actualizados correctamente');
    }
  </script>
</body>
</html>
