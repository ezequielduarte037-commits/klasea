<?php
require_once __DIR__ . '/config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        header('Location: /login.php?error=empty');
        exit;
    }

    $stmt = $mysqli->prepare('SELECT id, password FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['last_active'] = time();
        header('Location: /panel.php');
        exit;
    }

    header('Location: /login.php?error=invalid');
    exit;
}

if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: /login.php');
    exit;
}

http_response_code(400);
echo 'Acción no válida';
