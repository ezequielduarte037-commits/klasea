<?php
require_once __DIR__ . '/php/config.php';
if (is_logged_in()) {
  header('Location: /panel.php');
} else {
  header('Location: /login.php');
}
exit;
