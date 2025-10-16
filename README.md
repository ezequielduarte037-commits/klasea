# Klase A - Panel Operativo

Sistema de panel operativo personalizado para propietarios de embarcaciones del astillero Klase A.

## 🚀 Características

- **Sistema de Login**: Autenticación segura con PHP y MySQL
- **Panel Personalizado**: Cada propietario ve su embarcación específica
- **Diseño Premium**: Mantiene la estética elegante y sobria original
- **Personalización por Modelo**: Contenido adaptado según el modelo de barco (K85, K64, K52, K43, K42, K37, K34)
- **Sistema de Administración**: Gestión completa de usuarios
- **Interactividad**: Navegación fluida sin recargas de página

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

## 🛠️ Instalación

### 1. Configurar Base de Datos

```sql
-- Ejecutar el archivo php/database.sql en MySQL
mysql -u root -p < php/database.sql
```

### 2. Configurar Conexión

Editar `php/config.php` con tus credenciales de base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'klasea_clients');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 3. Subir Archivos

Subir todos los archivos al directorio web de tu servidor.

### 4. Configurar Permisos

```bash
chmod 755 assets/img/modelos/
chmod 644 php/*.php
```

## 🎯 Uso

### Acceso al Sistema

1. **Login**: Visitar `login.php`
   - Email: `carlos.perez@email.com`
   - Contraseña: `password`

2. **Panel Personalizado**: Después del login, acceder a `panel.php`

3. **Administración**: Acceder a `admin/index.php`
   - Email: `admin@klasea.com`
   - Contraseña: `password`

### Usuarios de Prueba

El sistema incluye usuarios de ejemplo para diferentes modelos:

| Email | Contraseña | Modelo |
|-------|------------|--------|
| carlos.perez@email.com | password | K85 |
| maria.gonzalez@email.com | password | K64 |
| roberto.silva@email.com | password | K52 |
| ana.martinez@email.com | password | K43 |
| luis.fernandez@email.com | password | K42 |
| patricia.lopez@email.com | password | K37 |
| diego.rodriguez@email.com | password | K34 |

## 📁 Estructura del Proyecto

```
/
├── login.php                 # Página de login
├── panel.php                 # Panel principal personalizado
├── admin/                    # Sistema de administración
│   ├── index.php
│   ├── create_user.php
│   ├── update_user.php
│   ├── delete_user.php
│   └── get_user.php
├── php/                      # Backend PHP
│   ├── config.php           # Configuración y conexión DB
│   ├── auth.php             # Sistema de autenticación
│   ├── update_user.php      # Actualizar datos de usuario
│   └── database.sql         # Script de base de datos
├── assets/                   # Recursos estáticos
│   ├── css/
│   ├── js/
│   └── img/
│       └── modelos/         # Imágenes de embarcaciones
└── panel operaciones/       # Archivo original (referencia)
    └── index.html
```

## 🎨 Personalización

### Modelos de Embarcaciones

El sistema soporta los siguientes modelos:
- **K85**: 26m de eslora
- **K64**: 19.5m de eslora
- **K52**: 15.8m de eslora
- **K43**: 13.1m de eslora
- **K42**: 12.8m de eslora
- **K37**: 11.3m de eslora
- **K34**: 10.4m de eslora

### Imágenes Personalizadas

Agregar imágenes de embarcaciones en `assets/img/modelos/`:
- `klase-85.jpg`
- `klase-64.jpg`
- `klase-52.jpg`
- `klase-43.jpg`
- `klase-42.jpg`
- `klase-37.jpg`
- `klase-34.jpg`

## 🔧 Funcionalidades

### Panel Principal
- **Bienvenida Personalizada**: Saludo con nombre y modelo de barco
- **Navegación Interactiva**: Menú lateral con 8 secciones
- **Medidores en Tiempo Real**: Batería, combustible y agua
- **Configuración**: Actualización de datos personales
- **Sistemas**: Energía, propulsión, sistemas auxiliares
- **Seguridad**: Protocolos y equipos de emergencia
- **Tutoriales**: Guías y documentación

### Sistema de Administración
- **Gestión de Usuarios**: CRUD completo
- **Asignación de Modelos**: Vinculación con embarcaciones
- **Monitoreo de Accesos**: Último acceso de cada usuario
- **Control de Estado**: Activar/desactivar usuarios

## 🔒 Seguridad

- Contraseñas encriptadas con `password_hash()`
- Sesiones seguras con `session_start()`
- Validación de entrada en todos los formularios
- Protección contra inyección SQL con prepared statements
- Verificación de permisos de administrador

## 📱 Responsive Design

El panel mantiene el diseño original responsivo:
- Sidebar fijo de 280px en desktop
- Navegación adaptativa en móviles
- Tipografías Montserrat/Roboto
- Colores oscuros premium

## 🚀 Próximas Mejoras

- [ ] Dashboard con métricas en tiempo real
- [ ] Notificaciones push
- [ ] Sistema de reportes
- [ ] Integración con sensores IoT
- [ ] App móvil nativa

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo de Klase A.

---

**© 2025 Astillero Klase A - Marcando tendencia.**