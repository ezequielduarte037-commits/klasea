<?php
require_once __DIR__ . '/php/config.php';
require_login();
$user = current_user($mysqli);

// For dynamic welcome image per model
function model_welcome_image(?string $modelo): string {
    if (!$modelo) return '/img/welcome_default.jpg';
    $map = [
        '85' => '/img/models/85.jpg',
        '64' => '/img/models/64.jpg',
        '52' => '/img/models/52.jpg',
        '43' => '/img/models/43.jpg',
        '42' => '/img/models/42.jpg',
        '37' => '/img/models/37.jpg',
        '34' => '/img/models/34.jpg',
    ];
    return $map[$modelo] ?? '/img/welcome_default.jpg';
}

$welcomeImage = $user && !empty($user['imagen_unidad']) ? $user['imagen_unidad'] : model_welcome_image($user['modelo_barco'] ?? null);
$nombre = $user['nombre_completo'] ?? 'Propietario';
$hour = (int)date('G');
$greet = $hour < 12 ? 'Buenos días' : ($hour < 20 ? 'Buenas tardes' : 'Buenas noches');
?>
<?php
// We will load the existing static index.html markup into this page while injecting dynamic content via a tiny script
?>
<?php
// Output the original front-end as-is using an include of the HTML, but we need to place the file content here.
// Safer approach: serve a minimal wrapper that loads the same styles (Tailwind/Chart.js) and copies the exact skeleton but replaces dynamic headings and image.
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Klase A — Panel Operativo</title>
  <link rel="icon" href="/img/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
  <?php
    // Extract essential CSS variables from the original to preserve design.
  ?>
    :root {
      --bg-color: #0b0b0c;
      --text-primary: #ffffff;
      --text-secondary: #9aa0a6;
      --accent-color: #c6f432;
      --panel-bg: #111214;
      --panel-border: #1f2124;
      --font-display: 'Montserrat', system-ui, sans-serif;
      --font-body: 'Roboto', system-ui, sans-serif;
    }
    body { background: var(--bg-color); color: var(--text-primary); font-family: var(--font-body); }
    h1,h2,h3,h4 { font-family: var(--font-display); }
    .main-container { display:grid; grid-template-columns: 320px 1fr; min-height: 100vh; }
    aside { background:#0b0b0c; border-right:1px solid var(--panel-border); padding:2rem; position:sticky; top:0; height:100vh; display:flex; flex-direction:column; }
    main { padding:3rem; }
    .nav-item { display:flex; align-items:center; gap:1rem; color:var(--text-secondary); padding:.75rem 0; border-left:2px solid transparent; padding-left:1rem; text-decoration:none }
    .nav-item:hover { color:var(--text-primary); }
    .nav-item.active { color:var(--accent-color); border-left-color:var(--accent-color); }
    .nav-number { font-weight:700; color:#6b7280; }
    .nav-title { text-transform:uppercase; letter-spacing:.08em; font-size:.9rem; }
    .section-header h1 { font-size:2.25rem; font-weight:800; }
    .section-header p { color: var(--text-secondary); }
    .content-section { display:none; }
    .content-section.active { display:block; }
    .card { background: var(--panel-bg); border:1px solid var(--panel-border); border-radius:.75rem; padding:1.5rem; }
    .btn { background:#222428; border:1px solid #2b2e33; padding:.6rem 1rem; border-radius:.5rem; }
    .btn:hover { border-color:var(--accent-color); color: var(--accent-color); }
  </style>
</head>
<body>
  <div class="main-container">
    <aside>
      <div class="mb-12">
        <img id="main-logo" src="/img/logo.png" alt="Klase A Logo" class="h-12 mb-2">
        <p class="text-sm text-secondary italic mb-4">Marcando tendencia.</p>
        <h2 class="text-xl font-bold">Panel K42</h2>
        <div class="mt-3 text-sm text-gray-400"><?=$greet?>, <?=$nombre?>.</div>
      </div>
      <nav id="main-nav" class="flex-1"></nav>
      <div class="mt-8">
        <a class="btn inline-block" href="/php/auth.php?action=logout">Cerrar sesión</a>
      </div>
      <div class="mt-8 text-xs text-gray-500">© <?=date('Y')?> Astillero Klase A.</div>
    </aside>
    <main>
      <section id="bienvenida" class="content-section active"></section>
      <section id="configuracion" class="content-section"></section>
      <section id="resumen" class="content-section"></section>
      <section id="energia" class="content-section"></section>
      <section id="propulsion" class="content-section"></section>
      <section id="sistemas" class="content-section"></section>
      <section id="seguridad" class="content-section"></section>
      <section id="tutoriales" class="content-section"></section>
    </main>
  </div>

  <script>
    const editableContent = {
      mainLogoUrl: '/img/logo.png',
      welcomeImageUrl: <?=json_encode($welcomeImage)?>,
      maxFuel: 1000,
      maxWater: 500,
    };

    const PanelApp = (() => {
      const qs = s => document.querySelector(s);
      const qsa = s => Array.from(document.querySelectorAll(s));
      const state = { gauges: {} };

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
          { id: 'tutoriales', title: 'Tutoriales' }
        ];
        qs('#main-nav').innerHTML = navItems.map((item, index) => `
          <a href="#${item.id}" class="nav-item">
            <span class="nav-number">0${index + 1}</span>
            <span class="nav-title">${item.title}</span>
          </a>
        `).join('');

        qs('#bienvenida').innerHTML = `
          <div class="section-header">
            <h1>Bienvenido a Bordo</h1>
            <p>Este es su panel de propietario digital. Bienvenido, <?=htmlspecialchars($nombre)?>.</p>
          </div>
          <img src="${editableContent.welcomeImageUrl}" class="w-full rounded-lg mt-8 opacity-90 shadow-2xl" alt="Embarcación">
        `;

        qs('#configuracion').innerHTML = `
          <div class="section-header"><h1>Configuración del Panel</h1><p>Datos de su embarcación.</p></div>
          <div class="mt-8 grid grid-cols-2 gap-8">
            <div class="card"><h3 class="font-bold text-lg mb-4">Propietario</h3>
              <p class="text-sm text-gray-400">Nombre: <?=htmlspecialchars($nombre)?></p>
              <p class="text-sm text-gray-400">Modelo: <?=htmlspecialchars($user['modelo_barco'] ?? '')?></p>
            </div>
            <div class="card"><h3 class="font-bold text-lg mb-4">Niveles simulados</h3>
              <div><label>Voltaje Batería (V)</label><input type="number" id="voltageInput" class="w-full mt-2 px-3 py-2 rounded-md" step="0.1"></div>
              <div class="mt-4"><label>Combustible (Litros)</label><input type="number" id="fuelInput" class="w-full mt-2 px-3 py-2 rounded-md"></div>
              <div class="mt-4"><label>Agua Potable (Litros)</label><input type="number" id="waterInput" class="w-full mt-2 px-3 py-2 rounded-md"></div>
              <button id="saveTanks" class="btn mt-4">Guardar Niveles</button>
            </div>
          </div>`;

        qs('#resumen').innerHTML = `
          <div class="section-header"><h1>Resumen de Estado</h1><p>Vista rápida de sistemas críticos.</p></div>
          <div class="grid grid-cols-3 gap-8 mt-8">
            <div class="card text-center"><h3 class="font-bold uppercase text-gray-400">Batería (V)</h3><canvas id="batteryGauge"></canvas></div>
            <div class="card text-center"><h3 class="font-bold uppercase text-gray-400">Combustible (%)</h3><canvas id="fuelGauge"></canvas></div>
            <div class="card text-center"><h3 class="font-bold uppercase text-gray-400">Agua (%)</h3><canvas id="waterGauge"></canvas></div>
          </div>`;
      }

      function initTabs() {
        const navLinks = qsa('.nav-item');
        function showSection(hash) {
          const targetId = (hash || '#bienvenida').substring(1);
          navLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + targetId));
          qsa('.content-section').forEach(s => s.classList.toggle('active', s.id === targetId));
        }
        window.addEventListener('hashchange', () => showSection(location.hash));
        navLinks.forEach(a => a.addEventListener('click', () => setTimeout(() => showSection(location.hash), 0)));
        showSection(location.hash || '#bienvenida');
      }

      function createGauge(canvasId, max) {
        return new Chart(document.getElementById(canvasId).getContext('2d'), {
          type: 'doughnut',
          data: { datasets: [{ data: [0, max], backgroundColor: ['#333', 'rgba(255,255,255,0.1)'], borderWidth: 0, circumference: 270, rotation: 225 }] },
          options: { cutout: '80%', plugins: { tooltip: { enabled: false }, datalabels: { formatter: (v, c) => c.dataIndex === 0 ? `${v.toFixed(c.chart.canvas.id === 'batteryGauge' ? 1 : 0)}${c.chart.canvas.id !== 'batteryGauge' ? '%' : ''}` : null, color: 'var(--text-primary)', font: { size: 24, weight: '700', family: 'var(--font-display)' } } }, responsive: true, maintainAspectRatio: true }
        });
      }

      function initGauges() {
        // @ts-ignore
        Chart.register(ChartDataLabels);
        state.gauges.batt = createGauge('batteryGauge', 16);
        state.gauges.fuel = createGauge('fuelGauge', 100);
        state.gauges.water = createGauge('waterGauge', 100);
      }

      function wireInputs() {
        const data = { batteryVoltage: 12.8, fuelLiters: 400, waterLiters: 200 };
        function refresh() {
          state.gauges.batt?.data.datasets[0].data[0] = data.batteryVoltage;
          state.gauges.batt?.update();
          const fuelPct = (data.fuelLiters / editableContent.maxFuel) * 100;
          state.gauges.fuel?.data.datasets[0].data[0] = fuelPct;
          state.gauges.fuel?.update();
          const waterPct = (data.waterLiters / editableContent.maxWater) * 100;
          state.gauges.water?.data.datasets[0].data[0] = waterPct;
          state.gauges.water?.update();
        }
        const v = document.getElementById('voltageInput');
        const f = document.getElementById('fuelInput');
        const w = document.getElementById('waterInput');
        v.addEventListener('input', e => { data.batteryVoltage = parseFloat(e.target.value || '0'); refresh(); });
        f.addEventListener('input', e => { data.fuelLiters = parseFloat(e.target.value || '0'); refresh(); });
        w.addEventListener('input', e => { data.waterLiters = parseFloat(e.target.value || '0'); refresh(); });
        refresh();
      }

      function init() {
        populateContent();
        initTabs();
        initGauges();
        wireInputs();
      }
      return { init };
    })();

    document.addEventListener('DOMContentLoaded', PanelApp.init);
  </script>
</body>
</html>
