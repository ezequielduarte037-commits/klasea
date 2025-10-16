<?php
/**
 * Herramienta para generar hash de contraseñas
 * Útil para cambiar contraseñas de usuarios
 * 
 * Uso: php generate_hash.php <contraseña>
 * Ejemplo: php generate_hash.php miNuevaContraseña123
 */

if (php_sapi_name() !== 'cli') {
    die('Este script solo puede ejecutarse desde línea de comandos.');
}

if ($argc < 2) {
    echo "Uso: php generate_hash.php <contraseña>\n";
    echo "Ejemplo: php generate_hash.php miContraseña123\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\n";
echo "==============================================\n";
echo "  Generador de Hash - Klase A\n";
echo "==============================================\n";
echo "\n";
echo "Contraseña:  $password\n";
echo "Hash:        $hash\n";
echo "\n";
echo "Para actualizar en la base de datos:\n";
echo "---------------------------------------------\n";
echo "UPDATE usuarios SET password = '$hash' WHERE email = 'usuario@ejemplo.com';\n";
echo "\n";
echo "O para administradores:\n";
echo "---------------------------------------------\n";
echo "UPDATE administradores SET password = '$hash' WHERE usuario = 'admin';\n";
echo "\n";
echo "==============================================\n";
echo "\n";
?>
