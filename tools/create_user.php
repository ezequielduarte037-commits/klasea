<?php
/**
 * Crear usuario desde línea de comandos
 * 
 * Uso: php create_user.php
 */

require_once '../php/config.php';

if (php_sapi_name() !== 'cli') {
    die('Este script solo puede ejecutarse desde línea de comandos.');
}

echo "\n";
echo "==============================================\n";
echo "  Crear Nuevo Usuario - Klase A\n";
echo "==============================================\n";
echo "\n";

// Solicitar datos
echo "Nombre completo: ";
$nombre = trim(fgets(STDIN));

echo "Email: ";
$email = trim(fgets(STDIN));

echo "Contraseña: ";
$password = trim(fgets(STDIN));

echo "Modelo de barco (85, 64, 52, 43, 42, 37, 34): ";
$modelo = trim(fgets(STDIN));

echo "URL imagen (opcional, presione Enter para omitir): ";
$imagen = trim(fgets(STDIN));

// Validar
if (empty($nombre) || empty($email) || empty($password) || empty($modelo)) {
    echo "\n✗ Error: Todos los campos son obligatorios (excepto imagen)\n\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "\n✗ Error: Email no válido\n\n";
    exit(1);
}

if (!in_array($modelo, ['85', '64', '52', '43', '42', '37', '34'])) {
    echo "\n✗ Error: Modelo no válido\n\n";
    exit(1);
}

// Crear usuario
try {
    $db = getDBConnection();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO usuarios (nombre_completo, email, password, modelo_barco, imagen_unidad) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $nombre,
        $email,
        $hash,
        $modelo,
        $imagen ?: null
    ]);
    
    echo "\n";
    echo "==============================================\n";
    echo "  ✓ Usuario creado exitosamente!\n";
    echo "==============================================\n";
    echo "\n";
    echo "Detalles:\n";
    echo "  ID: " . $db->lastInsertId() . "\n";
    echo "  Nombre: $nombre\n";
    echo "  Email: $email\n";
    echo "  Modelo: K$modelo\n";
    echo "\n";
    echo "El usuario ya puede acceder al panel.\n";
    echo "\n";
    
} catch (PDOException $e) {
    echo "\n✗ Error al crear usuario: " . $e->getMessage() . "\n\n";
    exit(1);
}
?>
