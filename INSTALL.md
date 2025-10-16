# Guía de Instalación - Klase A Panel de Propietarios

## 📦 Requisitos del Sistema

### Software Requerido
- **Servidor Web:** Apache 2.4+ o Nginx
- **PHP:** Versión 7.4 o superior (recomendado PHP 8.0+)
- **Base de Datos:** MySQL 5.7+ o MariaDB 10.3+
- **Extensiones PHP necesarias:**
  - PDO
  - PDO_MySQL
  - session
  - json

### Verificar Versiones

```bash
# Verificar versión de PHP
php -v

# Verificar extensiones de PHP
php -m | grep -E 'PDO|mysql|session|json'

# Verificar MySQL
mysql --version
```

## 🔧 Instalación Paso a Paso

### Paso 1: Preparar el Entorno

#### En servidor local (XAMPP/WAMP/MAMP):

1. Descargar e instalar XAMPP/WAMP/MAMP
2. Iniciar Apache y MySQL desde el panel de control
3. Acceder a phpMyAdmin: http://localhost/phpmyadmin

#### En servidor remoto (Linux):

```bash
# Actualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Apache
sudo apt install apache2 -y

# Instalar PHP y extensiones
sudo apt install php php-mysql php-pdo php-json php-session -y

# Instalar MySQL
sudo apt install mysql-server -y

# Habilitar módulos de Apache
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Paso 2: Crear la Base de Datos

#### Opción A: Usando phpMyAdmin (Recomendado para principiantes)

1. Abrir phpMyAdmin en el navegador
2. Ir a la pestaña "SQL"
3. Copiar y pegar el contenido de `database/klasea_clients.sql`
4. Hacer clic en "Continuar" o "Ejecutar"

#### Opción B: Usando línea de comandos

```bash
# Conectar a MySQL
mysql -u root -p

# Crear base de datos
CREATE DATABASE klasea_clients CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Salir
exit

# Importar el archivo SQL
mysql -u root -p klasea_clients < database/klasea_clients.sql
```

#### Opción C: Importar manualmente

```bash
# Si tienes el archivo SQL
mysql -u root -p < /ruta/al/archivo/klasea_clients.sql
```

### Paso 3: Configurar el Sistema

1. **Abrir el archivo `php/config.php`**

2. **Modificar las credenciales de base de datos:**

```php
// CAMBIAR ESTOS VALORES
define('DB_HOST', 'localhost');        // Host de MySQL
define('DB_NAME', 'klasea_clients');   // Nombre de la BD
define('DB_USER', 'root');             // Usuario MySQL
define('DB_PASS', '');                 // Contraseña MySQL
```

3. **Configurar la URL del sitio:**

```php
// En desarrollo local
define('SITE_URL', 'http://localhost');

// En producción
define('SITE_URL', 'https://panel.klasea.com');
```

4. **Ajustar la zona horaria (opcional):**

```php
date_default_timezone_set('America/Argentina/Buenos_Aires');
```

### Paso 4: Configurar Permisos

#### En Linux/macOS:

```bash
# Permisos para directorios
find . -type d -exec chmod 755 {} \;

# Permisos para archivos
find . -type f -exec chmod 644 {} \;

# Permisos especiales para archivos de configuración
chmod 600 php/config.php
```

#### En Windows (XAMPP/WAMP):
- Los permisos se manejan automáticamente
- Asegurarse de que el directorio esté dentro de `htdocs` o `www`

### Paso 5: Subir Archivos al Servidor

#### Servidor Local:
```bash
# XAMPP
cp -r * /opt/lampp/htdocs/klasea/

# WAMP
cp -r * C:/wamp64/www/klasea/

# MAMP
cp -r * /Applications/MAMP/htdocs/klasea/
```

#### Servidor Remoto (via FTP/SFTP):
1. Usar FileZilla, WinSCP o similar
2. Conectar al servidor
3. Subir todos los archivos a la carpeta web (ej: `/var/www/html/` o `/public_html/`)

#### Servidor Remoto (via SSH):
```bash
# Conectar al servidor
ssh usuario@tu-servidor.com

# Navegar al directorio web
cd /var/www/html/

# Clonar o copiar archivos
# Si están en Git:
git clone <url-repositorio> klasea
cd klasea

# O subir con SCP desde tu máquina local:
# scp -r /ruta/local/* usuario@servidor:/var/www/html/klasea/
```

### Paso 6: Configurar Apache

#### Habilitar .htaccess

Editar `/etc/apache2/sites-available/000-default.conf` (o tu archivo de configuración):

```apache
<Directory /var/www/html/klasea>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Reiniciar Apache:
```bash
sudo systemctl restart apache2
```

### Paso 7: Configurar Virtual Host (Opcional pero Recomendado)

Crear archivo `/etc/apache2/sites-available/klasea.conf`:

```apache
<VirtualHost *:80>
    ServerName panel.klasea.local
    ServerAdmin admin@klasea.com
    DocumentRoot /var/www/html/klasea
    
    <Directory /var/www/html/klasea>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/klasea_error.log
    CustomLog ${APACHE_LOG_DIR}/klasea_access.log combined
</VirtualHost>
```

Habilitar el sitio:
```bash
sudo a2ensite klasea.conf
sudo systemctl reload apache2
```

Agregar al archivo `/etc/hosts`:
```
127.0.0.1   panel.klasea.local
```

### Paso 8: Configurar SSL/HTTPS (Producción)

#### Usando Let's Encrypt (Gratis):

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache -y

# Obtener certificado
sudo certbot --apache -d panel.klasea.com

# Renovación automática (ya incluida)
sudo certbot renew --dry-run
```

### Paso 9: Verificar Instalación

1. **Abrir en navegador:**
   - Local: `http://localhost/klasea/`
   - Producción: `https://panel.klasea.com/`

2. **Probar Login de Propietario:**
   - Email: `juan.perez@example.com`
   - Contraseña: `klase2025`

3. **Probar Panel Admin:**
   - URL: `http://localhost/klasea/admin/`
   - Usuario: `admin`
   - Contraseña: `admin2025`

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"

**Solución:**
1. Verificar que MySQL esté corriendo: `sudo systemctl status mysql`
2. Verificar credenciales en `php/config.php`
3. Verificar que la BD existe: `SHOW DATABASES;` en MySQL

### Error: "Call to undefined function password_hash()"

**Solución:**
- Actualizar PHP a versión 5.5 o superior
- Verificar: `php -v`

### Error: "404 Not Found" en URLs

**Solución:**
1. Verificar que mod_rewrite esté habilitado: `sudo a2enmod rewrite`
2. Verificar `AllowOverride All` en configuración de Apache
3. Reiniciar Apache: `sudo systemctl restart apache2`

### Error: "Session not working"

**Solución:**
1. Verificar permisos del directorio de sesiones:
   ```bash
   ls -la /var/lib/php/sessions
   sudo chmod 1777 /var/lib/php/sessions
   ```

### Imágenes no cargan

**Solución:**
1. Verificar que la carpeta `assets/img/` exista
2. Subir logo.png y favicon.ico a `assets/img/`
3. Verificar permisos: `chmod 644 assets/img/*`

## 🔐 Seguridad Post-Instalación

1. **Cambiar contraseñas por defecto:**
   ```sql
   -- Cambiar contraseña de admin
   UPDATE administradores 
   SET password = '$2y$10$NUEVA_HASH' 
   WHERE usuario = 'admin';
   ```

2. **Proteger archivos sensibles:**
   ```bash
   chmod 600 php/config.php
   chmod 600 database/*.sql
   ```

3. **Configurar firewall:**
   ```bash
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

4. **Backup automático de BD:**
   ```bash
   # Crear script de backup
   nano /home/usuario/backup_klasea.sh
   ```
   
   Contenido:
   ```bash
   #!/bin/bash
   mysqldump -u root -p'PASSWORD' klasea_clients > /home/backup/klasea_$(date +%Y%m%d).sql
   ```

   Hacer ejecutable y programar en cron:
   ```bash
   chmod +x /home/usuario/backup_klasea.sh
   crontab -e
   # Agregar: 0 2 * * * /home/usuario/backup_klasea.sh
   ```

## ✅ Checklist de Instalación

- [ ] PHP 7.4+ instalado y configurado
- [ ] MySQL 5.7+ instalado y corriendo
- [ ] Base de datos creada e importada
- [ ] Archivo config.php configurado
- [ ] Permisos de archivos establecidos
- [ ] Apache configurado con mod_rewrite
- [ ] .htaccess funcionando
- [ ] Login de prueba exitoso
- [ ] Panel admin accesible
- [ ] SSL configurado (producción)
- [ ] Contraseñas por defecto cambiadas
- [ ] Backup configurado

## 📞 Soporte

Si encuentras problemas durante la instalación:

1. Revisar logs de Apache: `/var/log/apache2/error.log`
2. Revisar logs de PHP: `/var/log/php/error.log`
3. Contactar soporte: soporte@klasea.com

---

**¡Instalación completada! 🎉**
