# API Interna - Klase A Panel

Este documento describe la estructura interna del sistema y cómo extenderlo.

## 🔧 Estructura del Sistema

### Clases Principales

#### `Auth` (php/auth.php)

Clase principal para manejo de autenticación.

**Métodos:**

```php
// Login de usuario
public function login($email, $password): array
// Retorna: ['success' => bool, 'redirect' => string, 'error' => string]

// Logout
public function logout(): void

// Obtener usuario actual
public function getCurrentUser(): ?array
```

**Ejemplo de uso:**

```php
$auth = new Auth();
$result = $auth->login('usuario@ejemplo.com', 'contraseña');

if ($result['success']) {
    header('Location: ' . $result['redirect']);
} else {
    echo $result['error'];
}
```

### Funciones Globales (php/config.php)

```php
// Obtener conexión PDO
getDBConnection(): PDO

// Verificar sesión activa
checkSession(): void

// Obtener saludo según hora
getSaludo(): string
```

## 🗄️ Estructura de Base de Datos

### Tabla: usuarios

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único (PK, AI) |
| nombre_completo | VARCHAR(255) | Nombre del propietario |
| email | VARCHAR(255) | Email (único) |
| password | VARCHAR(255) | Hash bcrypt |
| modelo_barco | VARCHAR(10) | Modelo: 85, 64, 52, etc. |
| imagen_unidad | VARCHAR(500) | URL imagen embarcación |
| fecha_registro | TIMESTAMP | Fecha de alta |
| ultimo_acceso | TIMESTAMP | Último login |
| activo | TINYINT(1) | 1=activo, 0=inactivo |

### Tabla: administradores

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único (PK, AI) |
| usuario | VARCHAR(100) | Username (único) |
| password | VARCHAR(255) | Hash bcrypt |
| nombre | VARCHAR(255) | Nombre completo |
| email | VARCHAR(255) | Email |
| fecha_registro | TIMESTAMP | Fecha de alta |

### Tabla: sesiones

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único (PK, AI) |
| usuario_id | INT | FK a usuarios.id |
| ip_address | VARCHAR(45) | IP del login |
| user_agent | TEXT | Navegador/dispositivo |
| fecha_login | TIMESTAMP | Fecha/hora login |

## 🔐 Sesiones

### Variables de Sesión

```php
$_SESSION['user_id']        // ID del usuario
$_SESSION['user_email']     // Email del usuario
$_SESSION['user_nombre']    // Nombre completo
$_SESSION['user_modelo']    // Modelo de barco
$_SESSION['user_imagen']    // URL imagen
$_SESSION['last_activity']  // Timestamp última actividad
```

### Proteger Páginas

```php
<?php
require_once 'php/config.php';
checkSession(); // Redirige a login si no hay sesión
?>
```

## 🎨 Personalización por Modelo

### Agregar Nuevo Modelo

1. **Actualizar panel.php:**

```php
$imagenes_modelos = [
    '85' => 'URL_IMAGEN_K85',
    '64' => 'URL_IMAGEN_K64',
    // ... modelos existentes
    '100' => 'URL_IMAGEN_K100', // NUEVO
];
```

2. **Actualizar admin/dashboard.php:**

```php
<select name="modelo" class="input-field">
    <option value="85">K85</option>
    <!-- ... modelos existentes -->
    <option value="100">K100</option> <!-- NUEVO -->
</select>
```

### Contenido Específico por Modelo

```php
// En panel.php
<?php if ($usuario['modelo_barco'] == '85'): ?>
    <!-- Contenido específico para K85 -->
<?php elseif ($usuario['modelo_barco'] == '64'): ?>
    <!-- Contenido específico para K64 -->
<?php else: ?>
    <!-- Contenido por defecto -->
<?php endif; ?>
```

## 📊 Sistema de Gauges

Los medidores circulares usan Chart.js:

```javascript
// Configuración base
const gaugeConfig = {
    type: 'doughnut',
    options: {
        cutout: '75%',
        plugins: {
            datalabels: {
                formatter: (value, context) => {
                    return context.chart.data.datasets[0].label;
                }
            }
        }
    }
};

// Crear gauge
new Chart(canvasElement, {
    ...gaugeConfig,
    data: {
        datasets: [{
            label: '12.6 V',
            data: [12.6, 14.4 - 12.6],
            backgroundColor: ['#10b981', '#1a1a1a']
        }]
    }
});
```

## 🔄 Agregar Nueva Sección

1. **Agregar en panel.php (navegación):**

```php
<a href="#nueva-seccion" class="nav-item" data-section="nueva-seccion">
    <span class="nav-number">09</span>
    <span class="nav-title">Nueva Sección</span>
</a>
```

2. **Agregar contenido:**

```php
<section id="nueva-seccion" class="content-section">
    <div class="section-header">
        <h1>Título de la Sección</h1>
        <p>Descripción...</p>
    </div>
    <!-- Contenido -->
</section>
```

El sistema de navegación lo detectará automáticamente.

## 🔌 Integración con APIs Externas

### Ejemplo: Guardar datos vía AJAX

```javascript
function guardarDatos(datos) {
    fetch('api/guardar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Guardado exitosamente', 'success');
        }
    });
}
```

### Crear endpoint API

```php
<?php
// api/guardar.php
require_once '../php/config.php';
checkSession();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// Procesar datos
$db = getDBConnection();
// ... lógica

echo json_encode(['success' => true]);
?>
```

## 🎯 Eventos JavaScript

### Sistema de Navegación

```javascript
// Cambiar sección programáticamente
function showSection(sectionId) {
    const navItem = document.querySelector(`[data-section="${sectionId}"]`);
    if (navItem) {
        navItem.click();
    }
}

// Usar
showSection('energia');
```

### Notificaciones

```javascript
// Mostrar notificación
showNotification('Mensaje aquí', 'success'); // o 'error'
```

## 🔒 Seguridad

### Validación de Entrada

```php
// Sanitizar datos
$nombre = filter_var($input['nombre'], FILTER_SANITIZE_STRING);
$email = filter_var($input['email'], FILTER_VALIDATE_EMAIL);

// Prevenir XSS
echo htmlspecialchars($dato_usuario, ENT_QUOTES, 'UTF-8');
```

### Consultas Preparadas

```php
// SIEMPRE usar prepared statements
$stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);

// NUNCA concatenar
// $sql = "SELECT * FROM usuarios WHERE email = '$email'"; // ❌
```

### CSRF Protection (opcional)

```php
// Generar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validar
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token inválido');
}
```

## 📝 Logging

### Agregar Sistema de Logs

```php
// Crear función de logging
function logActivity($userId, $action, $details = null) {
    $db = getDBConnection();
    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, action, details, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $action, json_encode($details)]);
}

// Usar
logActivity($_SESSION['user_id'], 'login', ['ip' => $_SERVER['REMOTE_ADDR']]);
```

## 🧪 Testing

### Test Manual

```bash
# Test conexión BD
php tools/test_db.php

# Crear usuario de prueba
php tools/create_user.php

# Generar hash de contraseña
php tools/generate_hash.php miContraseña123
```

## 📱 Responsive Design

El sistema usa Tailwind CSS. Clases útiles:

```html
<!-- Ocultar en móvil -->
<div class="hidden md:block">...</div>

<!-- Mostrar solo en móvil -->
<div class="md:hidden">...</div>

<!-- Grid responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">...</div>
```

## 🚀 Optimización

### Caché de Consultas

```php
// Guardar en sesión para evitar consultas repetidas
if (!isset($_SESSION['user_data'])) {
    $_SESSION['user_data'] = $auth->getCurrentUser();
}
$usuario = $_SESSION['user_data'];
```

### Lazy Loading de Imágenes

```html
<img src="imagen.jpg" loading="lazy" alt="...">
```

---

**Para más información, consultar el código fuente o contactar a soporte@klasea.com**
