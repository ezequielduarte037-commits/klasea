<?php
/**
 * Cerrar sesión - Klase A
 */

require_once 'config.php';
require_once 'auth.php';

$auth = new Auth();
$auth->logout();
?>
