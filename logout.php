<?php
require_once 'php/config.php';

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit();
?>