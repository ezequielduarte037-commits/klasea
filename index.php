<?php
/**
 * Página de inicio - Klase A Panel de Propietarios
 * Redirige automáticamente al panel si hay sesión activa, 
 * o al login si no la hay
 */

require_once 'php/config.php';

// Si hay sesión activa, ir al panel
if (isset($_SESSION['user_id'])) {
    header('Location: panel.php');
    exit;
}

// Si no hay sesión, ir al login
header('Location: login.php');
exit;
?>
