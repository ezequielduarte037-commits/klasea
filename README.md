# Klase A - Panel de Propietarios

Sistema de gestión y monitoreo para propietarios de embarcaciones del astillero Klase A.

## 📋 Características

- ✅ Sistema de login seguro con PHP y MySQL
- ✅ Panel personalizado por usuario y modelo de embarcación
- ✅ Diseño elegante y premium (negro, blanco, tipografía Montserrat/Roboto)
- ✅ Navegación interactiva por secciones sin recargar página
- ✅ Panel administrativo para gestión de usuarios
- ✅ Sesiones seguras con timeout automático
- ✅ Responsive design

## 🚀 Instalación

### 1. Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL

### 2. Configuración de Base de Datos

```bash
# Importar el archivo SQL
mysql -u root -p < database/klasea_clients.sql
```

O crear manualmente desde phpMyAdmin importando el archivo `database/klasea_clients.sql`

### 3. Configuración del Sistema

Editar el archivo `php/config.php` con los datos de su servidor:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'klasea_clients');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
define('SITE_URL', 'http://tu-dominio.com');
```

### 4. Estructura de Carpetas

```
/
├── admin/                  # Panel de administración
│   ├── index.php          # Login admin
│   ├── dashboard.php      # Dashboard admin
│   └── logout.php         # Cerrar sesión admin
├── assets/                # Recursos estáticos
│   └── img/              # Imágenes
│       ├── logo.png      # Logo del astillero
│       └── favicon.ico   # Favicon
├── database/             # Scripts SQL
│   └── klasea_clients.sql
├── php/                  # Backend PHP
│   ├── config.php       # Configuración
│   ├── auth.php         # Autenticación
│   └── logout.php       # Cerrar sesión
├── login.php            # Página de login
├── panel.php            # Panel principal
├── .htaccess           # Configuración Apache
└── README.md           # Este archivo
```

## 👥 Usuarios de Prueba

### Propietarios
- **Email:** juan.perez@example.com | **Contraseña:** klase2025
- **Email:** maria.gonzalez@example.com | **Contraseña:** klase2025
- **Email:** carlos.rodriguez@example.com | **Contraseña:** klase2025

### Administrador
- **Usuario:** admin | **Contraseña:** admin2025

## 🎨 Diseño

El sistema mantiene la estética premium del panel original:

- **Colores:** Negro (#000000), Blanco (#FFFFFF), Grises (#A0A0A0, #333333)
- **Tipografía:** Montserrat (títulos), Roboto (cuerpo)
- **Estilo:** Minimalista, elegante, tecnológico

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Protección contra SQL Injection (PDO con prepared statements)
- Sesiones seguras con timeout automático (1 hora)
- Validación de datos en cliente y servidor
- Headers de seguridad configurados

## 📱 Funcionalidades

### Panel de Propietarios
1. **Bienvenida:** Mensaje personalizado con imagen de la embarcación
2. **Configuración:** Datos del propietario y actualización de niveles
3. **Resumen:** Gauges circulares de batería, combustible y agua
4. **Sistema Eléctrico:** Control de cortes y tableros
5. **Propulsión:** Estado de motores y controles
6. **Sistemas a Bordo:** Agua, climatización, iluminación
7. **Seguridad:** Equipamiento y procedimientos de emergencia
8. **Tutoriales:** Guías paso a paso

### Panel Administrativo
- Alta, baja y modificación de usuarios
- Visualización de estadísticas
- Control de accesos y sesiones
- Gestión de modelos de embarcaciones

## 🔧 Personalización

### Agregar Nuevos Modelos

Editar `panel.php`, sección de imágenes por modelo:

```php
$imagenes_modelos = [
    '85' => 'URL_IMAGEN',
    '64' => 'URL_IMAGEN',
    // ... agregar nuevos modelos
];
```

### Cambiar Tiempo de Sesión

Editar `php/config.php`:

```php
define('SESSION_LIFETIME', 3600); // En segundos
```

## 🌐 Despliegue

### Desarrollo Local
```bash
# Opción 1: PHP Built-in Server
php -S localhost:8000

# Opción 2: XAMPP/WAMP/MAMP
# Copiar archivos a htdocs/www
```

### Producción
1. Subir archivos al servidor via FTP/SSH
2. Importar base de datos
3. Configurar permisos (chmod 755 para directorios, 644 para archivos)
4. Configurar SSL/HTTPS (recomendado)
5. Ajustar `SITE_URL` en config.php

## 📞 Soporte

Para soporte técnico o consultas:
- Email: soporte@klasea.com
- Web: www.klasea.com

## 📄 Licencia

© 2025 Astillero Klase A. Todos los derechos reservados.

---

**Desarrollado con ❤️ para los propietarios de Klase A**
