<?php
require_once 'php/config.php';

// Verificar que el usuario esté logueado
requireLogin();

// Obtener datos del usuario actual
$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// Función para obtener saludo personalizado según la hora
function getSaludo($nombre) {
    $hora = date('H');
    if ($hora >= 5 && $hora < 12) {
        $saludo = "Buenos días";
    } elseif ($hora >= 12 && $hora < 19) {
        $saludo = "Buenas tardes";
    } else {
        $saludo = "Buenas noches";
    }
    
    $nombre_parts = explode(' ', $nombre);
    $apellido = isset($nombre_parts[1]) ? ' Sr. ' . $nombre_parts[1] : '';
    
    return $saludo . $apellido;
}

// Función para obtener imagen personalizada según el modelo
function getImagenModelo($modelo) {
    $imagenes = [
        '85' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '64' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '52' => 'https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '43' => 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '42' => 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '37' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
        '34' => 'https://images.unsplash.com/photo-1544551763-77ef2d0cfc6c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80'
    ];
    
    return $user['imagen_unidad'] ?: ($imagenes[$modelo] ?? $imagenes['42']);
}

$saludo = getSaludo($user['nombre_completo']);
$imagen_modelo = getImagenModelo($user['modelo_barco']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Klase A — Panel Operativo "<?php echo htmlspecialchars($user['nombre_completo']); ?>"</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
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
    
    .content-section { display: none; }
    .content-section.active { display: block; animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

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
     }

    .card {
        background-color: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1.5rem;
    }

    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: transparent;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        color: var(--text-primary);
        font-family: var(--font-body);
    }
    .input-field:focus {
        outline: none;
        border-color: var(--accent-color);
    }

    .btn-primary {
        background-color: var(--accent-color);
        color: var(--bg-dark);
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 4px;
        font-family: var(--font-display);
        font-weight: 700;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #e0e0e0;
        transform: translateY(-1px);
    }

    .tab-button {
        background: transparent;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .tab-button.active {
        color: var(--text-primary);
        border-bottom: 2px solid var(--accent-color);
    }
    .tab-button:hover {
        color: var(--text-primary);
    }

    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .logout-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .logout-btn:hover {
        color: var(--text-primary);
        border-color: var(--accent-color);
    }
  </style>
</head>
<body>

  <div class="main-container">
    <aside id="side-nav">
        <div class="mb-12">
            <img id="main-logo" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAAAAAAb2I2mAAAXQ0lEQVR42u1dLWzjTBMeMFpppQVGQaahRqGRCoMCi4ICg0MMwwqLwsLKW9YDhUVFYUGn0jCTlRYMeD5g79pO3MZJ7aSn7129b++uTZMdz+488z9E/63/1n/r/2Cx/vJH+l8mi1X+R/5PEw+T0bhco2QYR/6VRETKv/KfIVDlFEbxcLJYP71+/M1QX9nfjz9P68WkoFSx4n+NiyaapOs/rkKUNPwNcH/W6SQy/xp5yXy9D+RI+FL9X6T4LgBgv54nv50oRcooUsTJ/TMuW8/3CYf3+X03j5iJBkm6xU/WNk0GRMzEv5FEnazeAIgVuYg6ESsA3laJ/o0E0mD+AkDgRC6mUMRBALzMB7+Me0TjDQAphOQBgV/T2/jC/F02Y6Kb30ZWRIYN0WDWdPnEibP4nqEiAvvFS7azAVHETHQrqGQiYs0Up1vANu++gIL3583jajGbTO7G4/HdZDJbrB43z+/7o1dWlgW2aaxYc/FZt6BQMdEg/QRyEdGwdu/r2SiJo6YdchQno9n6ffcFe60A+3TAxOqGgidaARBxcMdb/FxNE33A86a/q2Safh7/uoMTB2AV3QbeWSlis6gLwnwBwPtycp7dwJPle353q2+Tr4VmUkpdV+woRaSn26owFIFzAiBbz2N9rhhkJj2YrTMA4hyqBAq2U810ZQo1qeELIK5yqAQO+HyexpoUn2v7MTMrMvH0+TO8l39nAV6G6trG5CAFROTg+u3mQyLNis+29piY2ChNNJzv6kjpRByQXuc6MjMTMdH9HsjPkpQnapX8RK5z+UfyUDFLBCJwwP6eiP0WerQfNCmmeAMRW+zBid1bYDuPOvzkaL4F7D4Lx8SKYBNWF0=" alt="Klase A Logo" class="h-12 mb-2">
            <p class="text-sm text-secondary italic mb-4">Marcando tendencia.</p>
            <h2 class="font-bold uppercase tracking-widest text-sm text-secondary">Panel K<?php echo $user['modelo_barco']; ?></h2>
        </div>

        <div class="user-info">
            <i class="fas fa-user-circle text-2xl text-secondary"></i>
            <div class="flex-1">
                <p class="font-bold text-sm"><?php echo htmlspecialchars($user['nombre_completo']); ?></p>
                <p class="text-xs text-secondary">Klase A K<?php echo $user['modelo_barco']; ?></p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>

        <nav id="main-nav" class="flex-1">
        </nav>
        <div class="mt-auto text-xs text-secondary">
            <p>&copy; 2025 Astillero Klase A.</p>
        </div>
    </aside>
    <main>
      <section id="bienvenida" class="content-section"></section>
      <section id="configuracion" class="content-section"></section>
      <section id="resumen" class="content-section"></section>
      <section id="energia" class="content-section"></section>
      <section id="propulsion" class="content-section"></section>
      <section id="sistemas" class="content-section"></section>
      <section id="seguridad" class="content-section"></section>
      <section id="tutoriales" class="content-section"></section>
    </main>
    <div id="save-notification" class="fixed bottom-5 right-5 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg opacity-0 transform translate-y-10">
        <i class="fas fa-check-circle mr-2"></i>Guardado
    </div>
  </div>

  <script type="module">
    const PanelApp = (() => {
        const state = { gauges: {}, userData: <?php echo json_encode($user); ?> };
        const qs = s => document.querySelector(s);
        const qsa = s => Array.from(document.querySelectorAll(s));

        const editableContent = {
            mainLogoUrl: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAAAAAAb2I2mAAAXQ0lEQVR42u1dLWzjTBMeMFpppQVGQaahRqGRCoMCi4ICg0MMwwqLwsLKW9YDhUVFYUGn0jCTlRYMeD5g79pO3MZJ7aSn7129b++uTZMdz+488z9E/63/1n/r/2Cx/vJH+l8mi1X+R/5PEw+T0bhco2QYR/6VRETKv/KfIVDlFEbxcLJYP71+/M1QX9nfjz9P68WkoFSx4n+NiyaapOs/rkKUNPwNcH/W6SQy/xp5yXy9D+RI+FL9X6T4LgBgv54nv50oRcooUsTJ/TMuW8/3CYf3+X03j5iJBkm6xU/WNk0GRMzEv5FEnazeAIgVuYg6ESsA3laJ/o0E0mD+AkDgRC6mUMRBALzMB7+Me0TjDQAphOQBgV/T2/jC/F02Y6Kb30ZWRIYN0WDWdPnEibP4nqEiAvvFS7azAVHETHQrqGQiYs0Up1vANu++gIL3583jajGbTO7G4/HdZDJbrB43z+/7o1dWlgW2aaxYc/FZt6BQMdEg/QRyEdGwdu/r2SiJo6YdchQno9n6ffcFe60A+3TAxOqGgidaARBxcMdb/FxNE33A86a/q2Safh7/uoMTB2AV3QbeWSlis6gLwnwBwPtycp7dwJPle353q2+Tr4VmUkpdV+woRaSn26owFIFzAiBbz2N9rhhkJj2YrTMA4hyqBAq2U810ZQo1qeELIK5yqAQO+HyexpoUn2v7MTMrMvH0+TO8l39nAV6G6trG5CAFROTg+u3mQyLNis+29piY2ChNNJzv6kjpRByQXuc6MjMTMdH9HsjPkpQnapX8RK5z+UfyUDFLBCJwwP6eiP0WerQfNCmmeAMRW+zBid1bYDuPOvzkaL4F7D4Lx8SKYBNWF0=",
            welcomeImageUrl: "<?php echo $imagen_modelo; ?>",
            maxFuel: 400,
            maxWater: 200
        };

        function populateContent() {
            qs('#main-logo').src = editableContent.mainLogoUrl;
            
            const navItems = [
                { id: 'bienvenida', title: 'Bienvenida' },
                { id: 'configuracion', title: 'Configuración' },
                { id: 'resumen', title: 'Resumen de Estado' },
                { id: 'energia', title: 'Sistema Eléctrico' },
                { id: 'propulsion', title: 'Propulsión' },
                { id: 'sistemas', title: 'Sistemas a Bordo' },
                { id: 'seguridad', title: 'Seguridad' },
                { id: 'tutoriales', title: 'Tutoriales'}
            ];

            qs('#main-nav').innerHTML = navItems.map((item, index) => `
                <a href="#${item.id}" class="nav-item" data-section="${item.id}">
                    <span class="nav-number">0${index + 1}</span>
                    <span class="nav-title">${item.title}</span>
                </a>
            `).join('');

            // Contenido personalizado de bienvenida
            const saludoPersonalizado = "<?php echo $saludo; ?>";
            const nombreCompleto = "<?php echo htmlspecialchars($user['nombre_completo']); ?>";
            const modeloBarco = "<?php echo $user['modelo_barco']; ?>";
            
            qs('#bienvenida').innerHTML = `
                <div class="section-header">
                    <h1>${saludoPersonalizado}, ${nombreCompleto}</h1>
                    <p>Bienvenido a bordo de su Klase A K${modeloBarco}. Este es su panel de propietario digital, una herramienta interactiva diseñada para que tenga el control total de su embarcación. Desde aquí podrá consultar el estado de los sistemas en tiempo real, seguir procedimientos operativos paso a paso y acceder a guías rápidas de seguridad. Estamos aquí para asegurar que su experiencia de navegación sea siempre excepcional, <strong>marcando tendencia</strong> en cada detalle.</p>
                </div>
                <img src="${editableContent.welcomeImageUrl}" class="w-full rounded-lg mt-8 opacity-90 shadow-2xl">
            `;
            
            // Resto del contenido igual que el original...
            qs('#configuracion').innerHTML = `<div class="section-header"><h1>Configuración del Panel</h1><p>Personalice los datos de su embarcación.</p></div><div class="mt-8 grid grid-cols-2 gap-8"><div class="card"><h3 class="font-bold text-lg mb-4">Datos del Propietario</h3><label for="userNameInput">Nombre de Usuario</label><input type="text" id="userNameInput" class="input-field mt-2" value="${nombreCompleto}"><button id="saveUser" class="btn-primary mt-4">Guardar</button></div><div class="card"><h3 class="font-bold text-lg mb-4">Actualizar Niveles</h3><div><label>Voltaje Batería (V)</label><input type="number" id="voltageInput" class="input-field mt-2" step="0.1"></div><div class="mt-4"><label>Combustible (Litros)</label><input type="number" id="fuelInput" class="input-field mt-2"></div><div class="mt-4"><label>Agua Potable (Litros)</label><input type="number" id="waterInput" class="input-field mt-2"></div><button id="saveTanks" class="btn-primary mt-4">Guardar Niveles</button></div></div>`;
            
            qs('#resumen').innerHTML = `<div class="section-header"><h1>Resumen de Estado</h1><p>Vista rápida de los sistemas críticos de su K${modeloBarco}.</p></div><div class="grid grid-cols-3 gap-8 mt-8"><div class="card text-center"><h3 class="font-bold uppercase text-secondary">Batería (V)</h3><canvas id="batteryGauge"></canvas></div><div class="card text-center"><h3 class="font-bold uppercase text-secondary">Combustible (%)</h3><canvas id="fuelGauge"></canvas></div><div class="card text-center"><h3 class="font-bold uppercase text-secondary">Agua (%)</h3><canvas id="waterGauge"></canvas></div></div>`;

            // Continuar con el resto de las secciones...
            populateOtherSections();
        }

        function populateOtherSections() {
            // Función auxiliar para procedimientos
            function getProcedimientoHTML(pasos) {
                return `<ol class="space-y-3">${pasos.map((paso, i) => `<li class="flex items-start gap-3"><span class="bg-white text-black rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold flex-shrink-0 mt-0.5">${i + 1}</span><span>${paso}</span></li>`).join('')}</ol>`;
            }

            qs('#energia').innerHTML = `
                <div class="section-header"><h1>Sistema Eléctrico</h1><p>Gestión de cortes generales, tableros 12V y fuentes de energía 220V.</p></div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center"><i class="fas fa-car-battery mr-3 text-2xl text-blue-400"></i>Cortes Generales (Estribor)</h3>
                        <p class="text-secondary mb-4">Al abordar, active los cortes de los sistemas que vaya a utilizar (Motor, Servicio, etc.).</p>
                        <div class="bg-red-900 border border-red-600 p-4 rounded-lg">
                            <h4 class="font-bold flex items-center"><i class="fas fa-triangle-exclamation mr-2"></i>ADVERTENCIA CRÍTICA</h4>
                            <p class="mt-2 text-red-100">Al retirarse de la embarcación, corte <strong>TODAS</strong> las baterías sin excepción para prevenir descargas y garantizar la seguridad.</p>
                        </div>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center"><i class="fas fa-bolt mr-3 text-2xl text-yellow-400"></i>Tablero 12V (Salón)</h3>
                        <p class="text-secondary mb-4">Controle aquí los circuitos de corriente continua: luces, bombas, electrónica, etc.</p>
                        <div class="bg-blue-900 border border-blue-600 p-4 rounded-lg">
                             <h4 class="font-bold flex items-center"><i class="fas fa-lightbulb mr-2"></i>Recomendación</h4>
                            <p class="mt-2 text-blue-100">Active únicamente los circuitos en uso para optimizar el consumo de las baterías de servicio.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 card" data-tabs-container="energia-220v">
                     <h3 class="font-bold text-lg mb-4 flex items-center"><i class="fas fa-power-off mr-3 text-2xl text-green-400"></i>Gestión de Energía 220V</h3>
                     <div class="border-b border-gray-700 mb-4">
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <button class="tab-button active font-bold py-2 px-4 rounded-t-lg" data-tab-target="puerto">Modo Puerto</button>
                            <button class="tab-button font-bold py-2 px-4 rounded-t-lg" data-tab-target="grupo">Modo G. Electrógeno</button>
                            <button class="tab-button font-bold py-2 px-4 rounded-t-lg" data-tab-target="inverter">Modo Inverter</button>
                        </nav>
                     </div>
                     <div id="puerto" class="tab-content active">
                        <h4 class="font-bold mb-3 text-lg">Procedimiento Conexión a Puerto</h4>
                        ${getProcedimientoHTML([
                            'Conecte el cable amarillo a la toma del muelle y de la embarcación.',
                            'En el tablero 220V, posicione ambas selectoras en "C.A. TIERRA".',
                            'Active los interruptores térmicos de los equipos a utilizar.'
                        ])}
                        <div class="bg-yellow-900 border border-yellow-600 p-4 rounded-lg mt-4"><h4 class="font-bold">Importante</h4><p class="mt-2 text-yellow-100">Verifique que la conexión del cable esté firme para evitar sobrecalentamiento.</p></div>
                     </div>
                     <div id="grupo" class="tab-content">
                        <h4 class="font-bold mb-3 text-lg">Procedimiento Grupo Electrógeno</h4>
                        ${getProcedimientoHTML([
                            'Active el corte de batería "GRUPO" en el panel de estribor.',
                            'En el salón, presione una vez el botón de encendido del grupo.',
                            'Espere 10-15 segundos a que estabilice.',
                            'En el tablero 220V, posicione ambas selectoras en "C.A. GRUPO".',
                            'Active los interruptores de los equipos a utilizar.'
                        ])}
                        <div class="bg-blue-900 border border-blue-600 p-4 rounded-lg mt-4"><h4 class="font-bold">Consejo Operativo</h4><p class="mt-2 text-blue-100">Espere al menos 30 segundos tras el arranque antes de activar equipos de alto consumo como el aire acondicionado.</p></div>
                     </div>
                     <div id="inverter" class="tab-content">
                        <h4 class="font-bold mb-3 text-lg">Procedimiento Modo Inverter</h4>
                        ${getProcedimientoHTML([
                            'Localice el panel del cargador/inverter en el salón y póngalo en "ON".',
                            'En el tablero 220V, posicione la selectora pequeña en "C.A. CONVERTIDOR".',
                            'Active sólo los térmicos asociados: Heladera, TV, tomas, etc.'
                        ])}
                        <div class="bg-red-900 border border-red-600 p-4 rounded-lg mt-4"><h4 class="font-bold">Advertencia Crítica</h4><p class="mt-2 text-red-100">Este modo consume las baterías. Monitoree el voltímetro. Si el voltaje baja de <strong>11.8V</strong>, desconecte el sistema inmediatamente para evitar daños permanentes en las baterías.</p></div>
                     </div>
                </div>
            `;

            // Continuar con las demás secciones...
            qs('#propulsion').innerHTML = `<div class="section-header"><h1>Sistema de Propulsión</h1><p>Procedimientos de arranque, navegación y mantenimiento de motores.</p></div><div class="mt-8 card"><h3 class="font-bold text-lg mb-4">En desarrollo...</h3><p class="text-secondary">Esta sección estará disponible próximamente con información específica para su modelo K${state.userData.modelo_barco}.</p></div>`;
            
            qs('#sistemas').innerHTML = `<div class="section-header"><h1>Sistemas a Bordo</h1><p>Control y monitoreo de sistemas auxiliares.</p></div><div class="mt-8 card"><h3 class="font-bold text-lg mb-4">En desarrollo...</h3><p class="text-secondary">Esta sección estará disponible próximamente.</p></div>`;
            
            qs('#seguridad').innerHTML = `<div class="section-header"><h1>Seguridad</h1><p>Protocolos de emergencia y equipamiento de seguridad.</p></div><div class="mt-8 card"><h3 class="font-bold text-lg mb-4">En desarrollo...</h3><p class="text-secondary">Esta sección estará disponible próximamente.</p></div>`;
            
            qs('#tutoriales').innerHTML = `<div class="section-header"><h1>Tutoriales</h1><p>Guías paso a paso para el manejo de su embarcación.</p></div><div class="mt-8 card"><h3 class="font-bold text-lg mb-4">En desarrollo...</h3><p class="text-secondary">Esta sección estará disponible próximamente.</p></div>`;
        }

        function initNavigation() {
            const navLinks = qsa('.nav-item');
            const sections = qsa('.content-section');

            function showSection(targetId) {
                sections.forEach(section => section.classList.remove('active'));
                navLinks.forEach(link => link.classList.remove('active'));
                
                const targetSection = qs(`#${targetId}`);
                const targetLink = qs(`[data-section="${targetId}"]`);
                
                if (targetSection) targetSection.classList.add('active');
                if (targetLink) targetLink.classList.add('active');
            }

            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const sectionId = link.getAttribute('data-section');
                    showSection(sectionId);
                });
            });

            // Mostrar bienvenida por defecto
            showSection('bienvenida');
        }

        function initTabs() {
            const tabButtons = qsa('.tab-button');
            const tabContents = qsa('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const container = button.closest('[data-tabs-container]');
                    const target = button.getAttribute('data-tab-target');
                    
                    // Desactivar todos los tabs del contenedor
                    container.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                    container.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    
                    // Activar el tab seleccionado
                    button.classList.add('active');
                    const targetContent = container.querySelector(`#${target}`);
                    if (targetContent) targetContent.classList.add('active');
                });
            });
        }

        function init() {
            populateContent();
            initNavigation();
            initTabs();
        }

        return { init };
    })();

    document.addEventListener('DOMContentLoaded', PanelApp.init);
  </script>
</body>
</html>