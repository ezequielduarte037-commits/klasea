# Guía de Instalación - Panel Operativo Editorial

## Pasos para instalar en htdocs

### 1. Copiar archivos a htdocs
Copia todos los archivos de esta carpeta a tu directorio htdocs de XAMPP/WAMP/LAMP:
```
C:\xampp\htdocs\panel_operaciones\ (Windows)
/opt/lampp/htdocs/panel_operaciones/ (Linux)
/Applications/XAMPP/htdocs/panel_operaciones/ (Mac)
```

### 2. Configurar base de datos
1. Abre phpMyAdmin (http://localhost/phpmyadmin)
2. Crea una nueva base de datos llamada: `panel_operaciones`
3. Importa el archivo `database.sql` que está en la carpeta `database/`

### 3. Configurar conexión a base de datos
Edita el archivo `config/database.php` y modifica:
- `DB_HOST`: tu servidor de base de datos (generalmente 'localhost')
- `DB_NAME`: nombre de tu base de datos
- `DB_USER`: usuario de MySQL
- `DB_PASS`: contraseña de MySQL

### 4. Configurar permisos (Linux/Mac)
```bash
chmod 755 -R /ruta/a/htdocs/panel_operaciones/
chmod 644 /ruta/a/htdocs/panel_operaciones/config/database.php
```

### 5. Acceder al panel
Abre tu navegador y ve a:
```
http://localhost/panel_operaciones/
```

## Estructura de archivos necesarios:
- index.html (archivo principal)
- config/ (configuración de base de datos)
- database/ (archivos SQL)
- assets/ (imágenes, CSS, JS)
- includes/ (archivos PHP)
- login.php (sistema de login)

## Credenciales por defecto:
- Usuario: admin
- Contraseña: admin123

¡IMPORTANTE! Cambia estas credenciales después de la primera instalación.