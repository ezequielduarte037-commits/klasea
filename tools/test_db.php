<?php
/**
 * Test de conexión a base de datos
 * Verifica que la configuración sea correcta
 * 
 * Uso: php test_db.php
 */

require_once '../php/config.php';

echo "\n";
echo "==============================================\n";
echo "  Test de Conexión - Klase A\n";
echo "==============================================\n";
echo "\n";

echo "Configuración:\n";
echo "  Host: " . DB_HOST . "\n";
echo "  Database: " . DB_NAME . "\n";
echo "  User: " . DB_USER . "\n";
echo "  Password: " . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : '(vacío)') . "\n";
echo "\n";

try {
    echo "Intentando conectar...\n";
    $db = getDBConnection();
    echo "✓ Conexión exitosa!\n";
    echo "\n";
    
    // Verificar tablas
    echo "Verificando tablas...\n";
    $tables = ['usuarios', 'administradores', 'sesiones'];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "  ✓ Tabla '$table': {$result['count']} registros\n";
    }
    
    echo "\n";
    echo "==============================================\n";
    echo "  ✓ Todo está funcionando correctamente!\n";
    echo "==============================================\n";
    echo "\n";
    
} catch (PDOException $e) {
    echo "✗ Error de conexión!\n";
    echo "  Mensaje: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Posibles soluciones:\n";
    echo "  1. Verificar que MySQL esté corriendo\n";
    echo "  2. Verificar las credenciales en php/config.php\n";
    echo "  3. Verificar que la base de datos exista\n";
    echo "  4. Verificar permisos del usuario MySQL\n";
    echo "\n";
    echo "==============================================\n";
    echo "\n";
    exit(1);
}
?>
