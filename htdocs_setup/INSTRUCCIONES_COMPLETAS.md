# 🚀 Guía Completa de Instalación - Panel Operativo Editorial

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:
- **XAMPP** (recomendado) o WAMP/LAMP
- **PHP 7.4+** con extensiones PDO y MySQL
- **MySQL 5.7+** o MariaDB 10.3+
- **Navegador web** moderno

## 🔧 Pasos de Instalación

### Paso 1: Preparar el Entorno
1. **Inicia XAMPP** y asegúrate de que estén corriendo:
   - Apache
   - MySQL

2. **Navega a la carpeta htdocs:**
   - Windows: `C:\xampp\htdocs\`
   - Linux: `/opt/lampp/htdocs/`
   - Mac: `/Applications/XAMPP/htdocs/`

### Paso 2: Copiar Archivos
1. **Crea una nueva carpeta** llamada `panel_operaciones` en htdocs
2. **Copia todos los archivos** de esta carpeta a `htdocs/panel_operaciones/`

La estructura final debe quedar así:
```
htdocs/
└── panel_operaciones/
    ├── index.html
    ├── login.php
    ├── logout.php
    ├── install.php
    ├── .htaccess
    ├── README_INSTALACION.md
    ├── INSTRUCCIONES_COMPLETAS.md
    ├── config/
    │   └── database.php
    ├── database/
    │   └── panel_operaciones.sql
    ├── includes/
    │   └── auth.php
    └── api/
        ├── operaciones.php
        └── metricas.php
```

### Paso 3: Instalación Automática
1. **Abre tu navegador** y ve a: `http://localhost/panel_operaciones/install.php`
2. **Completa el formulario** con los datos de tu MySQL:
   - Servidor: `localhost`
   - Base de datos: `panel_operaciones`
   - Usuario: `root`
   - Contraseña: (deja vacío si no tienes)
3. **Haz clic en "Instalar Panel"**
4. **Espera** a que se complete la instalación

### Paso 4: Verificar Instalación
1. **Ve a:** `http://localhost/panel_operaciones/`
2. **Inicia sesión** con las credenciales por defecto:
   - Usuario: `admin`
   - Contraseña: `admin123`

## 🔐 Configuración de Seguridad

### Cambiar Credenciales por Defecto
1. **Accede a phpMyAdmin:** `http://localhost/phpmyadmin`
2. **Selecciona la base de datos** `panel_operaciones`
3. **Ve a la tabla** `usuarios`
4. **Edita el usuario admin** y cambia la contraseña:
   ```sql
   UPDATE usuarios 
   SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
   WHERE username = 'admin';
   ```
   (Esta es la contraseña hasheada para "admin123")

### Configuración de Permisos (Linux/Mac)
```bash
# Navegar a la carpeta del proyecto
cd /ruta/a/htdocs/panel_operaciones/

# Dar permisos correctos
chmod 755 -R .
chmod 644 config/database.php
chmod 644 .htaccess
```

## 📊 Funcionalidades del Panel

### Dashboard Principal
- **Métricas en tiempo real** de operaciones
- **Gráficos interactivos** de estado y prioridad
- **Vista general** del rendimiento del sistema

### Gestión de Operaciones
- **Crear, editar y eliminar** operaciones
- **Asignar usuarios** a operaciones
- **Establecer prioridades** y fechas de vencimiento
- **Seguimiento de estados** (pendiente, en proceso, completado, cancelado)

### Sistema de Métricas
- **Análisis de tendencias** de los últimos 30 días
- **Estadísticas por tipo** de operación
- **Indicadores de eficiencia** y rendimiento

### Reportes
- **Generación de reportes** en PDF, Excel y CSV
- **Análisis detallado** por usuario y período
- **Exportación de datos** para análisis externos

## 🛠️ Mantenimiento

### Respaldos de Base de Datos
```bash
# Crear respaldo
mysqldump -u root -p panel_operaciones > backup_$(date +%Y%m%d).sql

# Restaurar respaldo
mysql -u root -p panel_operaciones < backup_20240215.sql
```

### Actualización de Datos
- El panel se actualiza automáticamente cada vez que accedes
- Los gráficos se refrescan en tiempo real
- Las métricas se calculan dinámicamente

### Limpieza de Cache
- El sistema no utiliza cache persistente
- Los datos se cargan directamente desde la base de datos
- Para limpiar sesiones, reinicia Apache

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
1. **Verifica que MySQL esté corriendo**
2. **Revisa las credenciales** en `config/database.php`
3. **Asegúrate de que la base de datos existe**

### Error 500 - Internal Server Error
1. **Revisa los logs de Apache** en XAMPP
2. **Verifica los permisos** de archivos
3. **Comprueba la sintaxis PHP**

### Página en Blanco
1. **Habilita la visualización de errores** en PHP
2. **Revisa la configuración** de PHP
3. **Verifica que todas las extensiones** estén habilitadas

### Problemas de Permisos
```bash
# En Linux/Mac, ejecuta:
sudo chown -R www-data:www-data /ruta/a/htdocs/panel_operaciones/
chmod -R 755 /ruta/a/htdocs/panel_operaciones/
```

## 📞 Soporte

Si encuentras problemas durante la instalación:

1. **Revisa los logs** de Apache y MySQL
2. **Verifica la configuración** de PHP
3. **Asegúrate de que todos los archivos** estén en su lugar
4. **Comprueba los permisos** de archivos y carpetas

## 🎯 Próximos Pasos

Después de la instalación exitosa:

1. **Cambia las credenciales** por defecto
2. **Configura usuarios adicionales** según necesites
3. **Personaliza las operaciones** según tu flujo de trabajo
4. **Configura respaldos automáticos** de la base de datos
5. **Revisa la configuración de seguridad** del servidor

---

**¡Felicidades!** 🎉 Tu Panel Operativo Editorial está listo para usar.