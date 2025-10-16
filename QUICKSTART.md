# 🚀 Guía de Inicio Rápido - Klase A Panel

## ⚡ Instalación en 5 Minutos

### 1. Requisitos Previos
- PHP 7.4+
- MySQL 5.7+
- Apache o Nginx

### 2. Instalación Automática (Linux)

```bash
# Dar permisos de ejecución
chmod +x install.sh

# Ejecutar instalador
sudo ./install.sh
```

### 3. Instalación Manual

```bash
# 1. Crear base de datos
mysql -u root -p < database/klasea_clients.sql

# 2. Configurar conexión
cp php/config.example.php php/config.php
nano php/config.php  # Editar credenciales

# 3. Configurar permisos
chmod 600 php/config.php
```

### 4. Acceder al Sistema

**Panel de Propietarios:**
```
URL: http://localhost/login.php
Email: juan.perez@example.com
Contraseña: klase2025
```

**Panel Admin:**
```
URL: http://localhost/admin/
Usuario: admin
Contraseña: admin2025
```

## 📝 Primeros Pasos

### Crear un Nuevo Usuario

**Opción 1: Desde el Panel Admin**
1. Ir a http://localhost/admin/
2. Login con admin/admin2025
3. Completar formulario "Agregar Nuevo Usuario"
4. Hacer clic en "Agregar Usuario"

**Opción 2: Desde CLI**
```bash
php tools/create_user.php
# Seguir las instrucciones
```

### Cambiar Contraseñas

```bash
# Generar nuevo hash
php tools/generate_hash.php MiNuevaContraseña123

# Copiar el hash generado y ejecutar en MySQL:
UPDATE usuarios SET password = 'HASH_GENERADO' WHERE email = 'usuario@ejemplo.com';
```

### Verificar Instalación

```bash
# Test de conexión a BD
php tools/test_db.php
```

## 🎨 Personalización Básica

### Cambiar Logo

```bash
# Subir logo del astillero (formato PNG, 200x60px recomendado)
cp /ruta/al/logo.png assets/img/logo.png
```

### Cambiar Favicon

```bash
# Subir favicon (32x32px, formato ICO)
cp /ruta/al/favicon.ico assets/img/favicon.ico
```

### Actualizar Imagen de Embarcación

```sql
UPDATE usuarios 
SET imagen_unidad = 'https://url-de-la-imagen.jpg' 
WHERE email = 'usuario@ejemplo.com';
```

## 🔧 Configuración

### Cambiar Tiempo de Sesión

Editar `php/config.php`:

```php
// 1 hora = 3600
// 2 horas = 7200
// 24 horas = 86400
define('SESSION_LIFETIME', 3600);
```

### Cambiar URL del Sitio

```php
// En desarrollo
define('SITE_URL', 'http://localhost');

// En producción
define('SITE_URL', 'https://panel.klasea.com');
```

### Cambiar Zona Horaria

```php
date_default_timezone_set('America/Argentina/Buenos_Aires');
```

## 🚨 Solución Rápida de Problemas

### Error de Conexión a BD
```bash
# Verificar que MySQL esté corriendo
sudo systemctl status mysql

# Verificar credenciales en php/config.php
cat php/config.php | grep "define('DB_"
```

### Sesiones no Funcionan
```bash
# Verificar permisos de sesiones PHP
ls -la /var/lib/php/sessions
sudo chmod 1777 /var/lib/php/sessions
```

### Error 404 en URLs
```bash
# Habilitar mod_rewrite en Apache
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Página en Blanco
```bash
# Ver errores de PHP
tail -f /var/log/apache2/error.log

# O activar display_errors en desarrollo
echo "display_errors = On" >> /etc/php/8.1/apache2/php.ini
sudo systemctl restart apache2
```

## 📊 Comandos Útiles

```bash
# Ver logs de Apache
tail -f /var/log/apache2/error.log

# Ver logs de MySQL
tail -f /var/log/mysql/error.log

# Backup de base de datos
mysqldump -u root -p klasea_clients > backup_$(date +%Y%m%d).sql

# Restaurar base de datos
mysql -u root -p klasea_clients < backup_20251016.sql

# Ver usuarios en BD
mysql -u root -p -e "SELECT id, nombre_completo, email, modelo_barco FROM klasea_clients.usuarios"

# Ver última actividad
mysql -u root -p -e "SELECT u.nombre_completo, s.fecha_login, s.ip_address FROM klasea_clients.sesiones s JOIN klasea_clients.usuarios u ON s.usuario_id = u.id ORDER BY s.fecha_login DESC LIMIT 10"
```

## 🔐 Seguridad Básica

### Cambiar Contraseña de Admin

```bash
# 1. Generar hash
php tools/generate_hash.php NuevaContraseñaSegura123!

# 2. Actualizar en BD
mysql -u root -p -e "UPDATE klasea_clients.administradores SET password = 'HASH_GENERADO' WHERE usuario = 'admin'"
```

### Configurar SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obtener certificado
sudo certbot --apache -d panel.klasea.com

# Renovación automática (ya configurada)
sudo certbot renew --dry-run
```

### Configurar Firewall

```bash
# Permitir HTTP y HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## 📱 URLs del Sistema

| Página | URL | Descripción |
|--------|-----|-------------|
| Inicio | `/` o `/index.php` | Redirección automática |
| Login | `/login.php` | Login propietarios |
| Panel | `/panel.php` | Panel principal |
| Admin Login | `/admin/` | Login administradores |
| Admin Panel | `/admin/dashboard.php` | Dashboard admin |

## 🎯 Checklist Post-Instalación

- [ ] Base de datos importada correctamente
- [ ] Archivo config.php configurado
- [ ] Login funciona con usuarios de prueba
- [ ] Panel admin accesible
- [ ] Logo y favicon personalizados
- [ ] Contraseñas por defecto cambiadas
- [ ] SSL/HTTPS configurado (producción)
- [ ] Backup automático configurado
- [ ] Permisos de archivos correctos
- [ ] Logs monitoreados

## 📞 Ayuda

**Documentación Completa:**
- README.md - Guía principal
- INSTALL.md - Instalación detallada
- API.md - Documentación técnica

**Soporte:**
- Email: soporte@klasea.com
- Web: www.klasea.com

---

**¡Listo para navegar! ⚓**
