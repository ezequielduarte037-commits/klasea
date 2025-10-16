<?php
require_once 'php/config.php';
requireLogin();

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

$greeting = getGreeting();
$modelo = $user['modelo_barco'];
$imagen_unidad = $user['imagen_unidad'] ?: 'assets/img/modelos/klase-' . $modelo . '.jpg';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Klase A — Panel Operativo K<?php echo $modelo; ?></title>
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
        background-color: #111111;
        border: 1px solid var(--border-color);
        padding: 2rem;
        margin-bottom: 2rem;
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

    .tab-button {
        background-color: transparent;
        border: none;
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .tab-button.active {
        color: var(--accent-color);
        border-bottom: 2px solid var(--accent-color);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .checklist-item {
        background-color: #111111;
        border: 1px solid var(--border-color);
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .checklist-item:hover {
        border-color: var(--accent-color);
    }

    .checklist-item.completed {
        background-color: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.3);
    }

    .check-icon {
        transition: color 0.3s ease;
    }

    .check-icon.completed {
        color: #22c55e;
    }

    .logout-btn {
        background-color: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        padding: 0.5rem 1rem;
        font-family: var(--font-body);
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
            <img id="main-logo" src="" alt="Klase A Logo" class="h-12 mb-2">
            <p class="text-sm text-secondary italic mb-4">Marcando tendencia.</p>
            <h2 class="font-bold uppercase tracking-widest text-sm text-secondary">Panel K<?php echo $modelo; ?></h2>
        </div>
        <nav id="main-nav" class="flex-1">
        </nav>
        <div class="mt-auto text-xs text-secondary">
            <p>&copy; 2025 Astillero Klase A.</p>
            <button id="logoutBtn" class="logout-btn mt-4 w-full">
                <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
            </button>
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

  <script>
    // Datos del usuario desde PHP
    const userData = {
        id: <?php echo $user['id']; ?>,
        nombre: '<?php echo addslashes($user['nombre_completo']); ?>',
        email: '<?php echo addslashes($user['email']); ?>',
        modelo: '<?php echo $modelo; ?>',
        imagen: '<?php echo $imagen_unidad; ?>',
        greeting: '<?php echo $greeting; ?>'
    };

    const PanelApp = (() => {
        const state = { gauges: {}, userId: userData.id };
        const qs = s => document.querySelector(s);
        const qsa = s => Array.from(document.querySelectorAll(s));

        // Elementos de navegación
        const navItems = [
            { id: 'bienvenida', title: 'Bienvenida', icon: 'fas fa-home' },
            { id: 'configuracion', title: 'Configuración', icon: 'fas fa-cog' },
            { id: 'resumen', title: 'Resumen', icon: 'fas fa-chart-line' },
            { id: 'energia', title: 'Energía', icon: 'fas fa-bolt' },
            { id: 'propulsion', title: 'Propulsión', icon: 'fas fa-anchor' },
            { id: 'sistemas', title: 'Sistemas', icon: 'fas fa-cogs' },
            { id: 'seguridad', title: 'Seguridad', icon: 'fas fa-shield-alt' },
            { id: 'tutoriales', title: 'Tutoriales', icon: 'fas fa-book' }
        ];

        function initNavigation() {
            qs('#main-nav').innerHTML = navItems.map((item, index) => `
                <a href="#${item.id}" class="nav-item">
                    <span class="nav-number">0${index + 1}</span>
                    <span class="nav-title">${item.title}</span>
                </a>
            `).join('');

            const navLinks = qsa('.nav-item');
            function showSection(hash) {
                const targetId = (hash || '#bienvenida').substring(1);
                navLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + targetId));
                qsa('.content-section').forEach(s => s.classList.toggle('active', s.id === targetId));
                history.replaceState(null, '', '#' + targetId);
            }
            
            navLinks.forEach(l => l.addEventListener('click', e => {
                e.preventDefault();
                showSection(l.getAttribute('href'));
            }));
            showSection(window.location.hash || '#bienvenida');
        }

        function initContent() {
            // Contenido personalizado por modelo
            const modeloInfo = getModeloInfo(userData.modelo);
            
            // Bienvenida personalizada
            qs('#bienvenida').innerHTML = `
                <div class="section-header">
                    <h1>${userData.greeting}, ${userData.nombre}</h1>
                    <p>Este es su panel de propietario digital para su Klase A K${userData.modelo}, una herramienta interactiva diseñada para que tenga el control total de su embarcación. Desde aquí podrá consultar el estado de los sistemas en tiempo real, seguir procedimientos operativos paso a paso y acceder a guías rápidas de seguridad. Estamos aquí para asegurar que su experiencia de navegación sea siempre excepcional, <strong>marcando tendencia</strong> en cada detalle.</p>
                </div>
                <img src="${userData.imagen}" class="w-full rounded-lg mt-8 opacity-90 shadow-2xl" alt="Klase A K${userData.modelo}">
                <div class="mt-6 card">
                    <h3 class="font-bold text-lg mb-4">Información de su Embarcación</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-secondary">Modelo:</p>
                            <p class="font-bold">Klase A K${userData.modelo}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Especificaciones:</p>
                            <p class="font-bold">${modeloInfo.especificaciones}</p>
                        </div>
                    </div>
                </div>
            `;

            // Configuración
            qs('#configuracion').innerHTML = `
                <div class="section-header">
                    <h1>Configuración del Panel</h1>
                    <p>Personalice los datos de su embarcación.</p>
                </div>
                <div class="mt-8 grid grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4">Datos del Propietario</h3>
                        <div class="mb-4">
                            <label for="userNameInput">Nombre Completo</label>
                            <input type="text" id="userNameInput" class="input-field mt-2" value="${userData.nombre}">
                        </div>
                        <div class="mb-4">
                            <label for="userEmailInput">Email</label>
                            <input type="email" id="userEmailInput" class="input-field mt-2" value="${userData.email}">
                        </div>
                        <button id="saveUser" class="btn-primary">Guardar</button>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4">Actualizar Niveles</h3>
                        <div>
                            <label>Voltaje Batería (V)</label>
                            <input type="number" id="voltageInput" class="input-field mt-2" step="0.1" value="12.6">
                        </div>
                        <div class="mt-4">
                            <label>Combustible (Litros)</label>
                            <input type="number" id="fuelInput" class="input-field mt-2" value="450">
                        </div>
                        <div class="mt-4">
                            <label>Agua Potable (Litros)</label>
                            <input type="number" id="waterInput" class="input-field mt-2" value="200">
                        </div>
                        <button id="saveTanks" class="btn-primary mt-4">Guardar Niveles</button>
                    </div>
                </div>
            `;

            // Resumen
            qs('#resumen').innerHTML = `
                <div class="section-header">
                    <h1>Resumen de Estado</h1>
                    <p>Vista rápida de los sistemas críticos de su Klase A K${userData.modelo}.</p>
                </div>
                <div class="grid grid-cols-3 gap-8 mt-8">
                    <div class="card text-center">
                        <h3 class="font-bold uppercase text-secondary">Batería (V)</h3>
                        <canvas id="batteryGauge" width="200" height="200"></canvas>
                    </div>
                    <div class="card text-center">
                        <h3 class="font-bold uppercase text-secondary">Combustible (%)</h3>
                        <canvas id="fuelGauge" width="200" height="200"></canvas>
                    </div>
                    <div class="card text-center">
                        <h3 class="font-bold uppercase text-secondary">Agua (%)</h3>
                        <canvas id="waterGauge" width="200" height="200"></canvas>
                    </div>
                </div>
            `;

            // Otras secciones (mantener el contenido original pero personalizado)
            initOtherSections();
        }

        function getModeloInfo(modelo) {
            const modelos = {
                '85': { especificaciones: 'Eslora: 26m, Manga: 6.5m, Calado: 1.8m' },
                '64': { especificaciones: 'Eslora: 19.5m, Manga: 5.2m, Calado: 1.4m' },
                '52': { especificaciones: 'Eslora: 15.8m, Manga: 4.6m, Calado: 1.2m' },
                '43': { especificaciones: 'Eslora: 13.1m, Manga: 4.0m, Calado: 1.0m' },
                '42': { especificaciones: 'Eslora: 12.8m, Manga: 3.9m, Calado: 0.9m' },
                '37': { especificaciones: 'Eslora: 11.3m, Manga: 3.5m, Calado: 0.8m' },
                '34': { especificaciones: 'Eslora: 10.4m, Manga: 3.2m, Calado: 0.7m' }
            };
            return modelos[modelo] || { especificaciones: 'Especificaciones del modelo' };
        }

        function initOtherSections() {
            // Energía
            qs('#energia').innerHTML = `
                <div class="section-header">
                    <h1>Sistema Eléctrico</h1>
                    <p>Gestión de cortes generales, tableros 12V y fuentes de energía 220V para su Klase A K${userData.modelo}.</p>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-car-battery mr-3 text-2xl text-blue-400"></i>Cortes Generales (Estribor)
                        </h3>
                        <p class="text-secondary mb-4">Al abordar, active los cortes de los sistemas que vaya a utilizar (Motor, Servicio, etc.).</p>
                        <div class="bg-red-900 border border-red-600 p-4 rounded-lg">
                            <h4 class="font-bold flex items-center">
                                <i class="fas fa-triangle-exclamation mr-2"></i>ADVERTENCIA CRÍTICA
                            </h4>
                            <p class="mt-2 text-red-100">Al retirarse de la embarcación, corte <strong>TODAS</strong> las baterías sin excepción para prevenir descargas y garantizar la seguridad.</p>
                        </div>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-bolt mr-3 text-2xl text-yellow-400"></i>Tablero 12V (Salón)
                        </h3>
                        <p class="text-secondary mb-4">Controle aquí los circuitos de corriente continua: luces, bombas, electrónica, etc.</p>
                        <div class="bg-blue-900 border border-blue-600 p-4 rounded-lg">
                            <h4 class="font-bold flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>Recomendación
                            </h4>
                            <p class="mt-2 text-blue-100">Active únicamente los circuitos en uso para optimizar el consumo de las baterías de servicio.</p>
                        </div>
                    </div>
                </div>
            `;

            // Propulsión
            qs('#propulsion').innerHTML = `
                <div class="section-header">
                    <h1>Sistema de Propulsión</h1>
                    <p>Control y monitoreo del sistema de propulsión de su Klase A K${userData.modelo}.</p>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-anchor mr-3 text-2xl text-blue-400"></i>Motores
                        </h3>
                        <p class="text-secondary mb-4">Estado y control de los motores principales.</p>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-cog mr-3 text-2xl text-green-400"></i>Transmisión
                        </h3>
                        <p class="text-secondary mb-4">Sistema de transmisión y hélices.</p>
                    </div>
                </div>
            `;

            // Sistemas
            qs('#sistemas').innerHTML = `
                <div class="section-header">
                    <h1>Sistemas Auxiliares</h1>
                    <p>Monitoreo de sistemas auxiliares de su Klase A K${userData.modelo}.</p>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-tint mr-3 text-2xl text-blue-400"></i>Agua
                        </h3>
                        <p class="text-secondary mb-4">Sistema de agua potable y aguas grises.</p>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-wind mr-3 text-2xl text-green-400"></i>Climatización
                        </h3>
                        <p class="text-secondary mb-4">Sistema de aire acondicionado y ventilación.</p>
                    </div>
                </div>
            `;

            // Seguridad
            qs('#seguridad').innerHTML = `
                <div class="section-header">
                    <h1>Seguridad y Emergencias</h1>
                    <p>Protocolos de seguridad y equipos de emergencia para su Klase A K${userData.modelo}.</p>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-life-ring mr-3 text-2xl text-red-400"></i>Equipos de Seguridad
                        </h3>
                        <p class="text-secondary mb-4">Chalecos salvavidas, balsas, extintores y equipos de emergencia.</p>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-phone mr-3 text-2xl text-green-400"></i>Comunicaciones
                        </h3>
                        <p class="text-secondary mb-4">Radio VHF, satelital y sistemas de comunicación de emergencia.</p>
                    </div>
                </div>
            `;

            // Tutoriales
            qs('#tutoriales').innerHTML = `
                <div class="section-header">
                    <h1>Tutoriales y Guías</h1>
                    <p>Guías paso a paso para el manejo de su Klase A K${userData.modelo}.</p>
                </div>
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-play mr-3 text-2xl text-blue-400"></i>Videos Tutoriales
                        </h3>
                        <p class="text-secondary mb-4">Videos explicativos de procedimientos operativos.</p>
                    </div>
                    <div class="card">
                        <h3 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-book mr-3 text-2xl text-green-400"></i>Manual de Usuario
                        </h3>
                        <p class="text-secondary mb-4">Documentación completa del sistema.</p>
                    </div>
                </div>
            `;
        }

        function initGauges() {
            // Inicializar medidores
            setTimeout(() => {
                if (typeof Chart !== 'undefined') {
                    initBatteryGauge();
                    initFuelGauge();
                    initWaterGauge();
                }
            }, 100);
        }

        function initBatteryGauge() {
            const ctx = document.getElementById('batteryGauge');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [85, 15],
                        backgroundColor: ['#22c55e', '#333333'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            display: true,
                            color: 'white',
                            font: { size: 24, weight: 'bold' },
                            formatter: () => '12.6V'
                        }
                    }
                }
            });
        }

        function initFuelGauge() {
            const ctx = document.getElementById('fuelGauge');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [75, 25],
                        backgroundColor: ['#f59e0b', '#333333'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            display: true,
                            color: 'white',
                            font: { size: 24, weight: 'bold' },
                            formatter: () => '75%'
                        }
                    }
                }
            });
        }

        function initWaterGauge() {
            const ctx = document.getElementById('waterGauge');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [60, 40],
                        backgroundColor: ['#3b82f6', '#333333'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        datalabels: {
                            display: true,
                            color: 'white',
                            font: { size: 24, weight: 'bold' },
                            formatter: () => '60%'
                        }
                    }
                }
            });
        }

        function initControls() {
            // Guardar datos del usuario
            qs('#saveUser')?.addEventListener('click', async () => {
                const newName = qs('#userNameInput').value.trim();
                const newEmail = qs('#userEmailInput').value.trim();
                
                if (newName && newEmail) {
                    try {
                        const response = await fetch('php/update_user.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `nombre=${encodeURIComponent(newName)}&email=${encodeURIComponent(newEmail)}`
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            showSaveNotification();
                            userData.nombre = newName;
                            userData.email = newEmail;
                        }
                    } catch (error) {
                        console.error('Error al guardar:', error);
                    }
                }
            });

            // Logout
            qs('#logoutBtn')?.addEventListener('click', async () => {
                try {
                    const response = await fetch('php/auth.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'action=logout'
                    });
                    
                    const result = await response.json();
                    if (result.success) {
                        window.location.href = 'login.php';
                    }
                } catch (error) {
                    console.error('Error al cerrar sesión:', error);
                    window.location.href = 'login.php';
                }
            });
        }

        function showSaveNotification() {
            const notification = qs('#save-notification');
            notification.classList.remove('opacity-0', 'translate-y-10');
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-y-10');
            }, 2000);
        }

        function init() {
            initNavigation();
            initContent();
            initGauges();
            initControls();
        }

        return { init };
    })();

    // Inicializar la aplicación
    document.addEventListener('DOMContentLoaded', () => {
        PanelApp.init();
    });
  </script>
</body>
</html>