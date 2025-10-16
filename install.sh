#!/bin/bash

# =====================================================
# Script de Instalación Rápida - Klase A Panel
# =====================================================

echo ""
echo "=============================================="
echo "  Klase A - Instalación del Panel"
echo "=============================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar si está ejecutándose como root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}✗ Este script debe ejecutarse como root${NC}"
    echo "  Intente: sudo ./install.sh"
    exit 1
fi

# Paso 1: Verificar dependencias
echo -e "${YELLOW}[1/6]${NC} Verificando dependencias..."

# Verificar PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP no está instalado${NC}"
    echo "  Instalando PHP..."
    apt update && apt install -y php php-mysql php-pdo php-json
else
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    echo -e "${GREEN}✓ PHP $PHP_VERSION instalado${NC}"
fi

# Verificar MySQL
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}✗ MySQL no está instalado${NC}"
    echo "  Instalando MySQL..."
    apt install -y mysql-server
else
    echo -e "${GREEN}✓ MySQL instalado${NC}"
fi

# Verificar Apache
if ! command -v apache2 &> /dev/null; then
    echo -e "${RED}✗ Apache no está instalado${NC}"
    echo "  Instalando Apache..."
    apt install -y apache2
else
    echo -e "${GREEN}✓ Apache instalado${NC}"
fi

# Paso 2: Configurar base de datos
echo ""
echo -e "${YELLOW}[2/6]${NC} Configurando base de datos..."

read -p "Usuario MySQL [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "Contraseña MySQL: " DB_PASS
echo ""

# Crear base de datos
echo "  Creando base de datos..."
mysql -u "$DB_USER" -p"$DB_PASS" < database/klasea_clients.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Base de datos creada exitosamente${NC}"
else
    echo -e "${RED}✗ Error al crear la base de datos${NC}"
    exit 1
fi

# Paso 3: Configurar archivo config.php
echo ""
echo -e "${YELLOW}[3/6]${NC} Configurando archivo de configuración..."

if [ -f "php/config.php" ]; then
    echo -e "${YELLOW}⚠ config.php ya existe. Haciendo backup...${NC}"
    cp php/config.php php/config.php.backup.$(date +%Y%m%d_%H%M%S)
fi

# Copiar desde ejemplo
cp php/config.example.php php/config.php

# Actualizar credenciales
sed -i "s/define('DB_USER', 'root');/define('DB_USER', '$DB_USER');/" php/config.php
sed -i "s/define('DB_PASS', '');/define('DB_PASS', '$DB_PASS');/" php/config.php

echo -e "${GREEN}✓ Archivo de configuración creado${NC}"

# Paso 4: Configurar permisos
echo ""
echo -e "${YELLOW}[4/6]${NC} Configurando permisos..."

# Permisos de directorios
find . -type d -exec chmod 755 {} \;

# Permisos de archivos
find . -type f -exec chmod 644 {} \;

# Permisos especiales
chmod 600 php/config.php
chmod +x tools/*.php

# Propietario
chown -R www-data:www-data .

echo -e "${GREEN}✓ Permisos configurados${NC}"

# Paso 5: Configurar Apache
echo ""
echo -e "${YELLOW}[5/6]${NC} Configurando Apache..."

# Habilitar mod_rewrite
a2enmod rewrite

# Reiniciar Apache
systemctl restart apache2

echo -e "${GREEN}✓ Apache configurado${NC}"

# Paso 6: Test de conexión
echo ""
echo -e "${YELLOW}[6/6]${NC} Probando conexión..."

php tools/test_db.php

if [ $? -eq 0 ]; then
    echo ""
    echo "=============================================="
    echo -e "${GREEN}  ✓ Instalación completada exitosamente!${NC}"
    echo "=============================================="
    echo ""
    echo "Accesos:"
    echo "  Panel Propietarios: http://localhost/login.php"
    echo "  Panel Admin: http://localhost/admin/"
    echo ""
    echo "Usuarios de prueba:"
    echo "  Email: juan.perez@example.com"
    echo "  Contraseña: klase2025"
    echo ""
    echo "Admin:"
    echo "  Usuario: admin"
    echo "  Contraseña: admin2025"
    echo ""
    echo "⚠ IMPORTANTE: Cambie las contraseñas por defecto"
    echo ""
else
    echo ""
    echo -e "${RED}✗ Error en la instalación${NC}"
    echo "  Revise los logs para más detalles"
    exit 1
fi
