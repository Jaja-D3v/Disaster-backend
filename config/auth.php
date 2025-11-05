<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => 'localhost',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'None'
]);
session_start();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    session_unset();
    session_destroy();
    http_response_code(401);
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

 json_encode([
    "success" => true,
    "user" => [
        "id" => $_SESSION['user_id'],
        "username" => $_SESSION['username'],
        "email" => $_SESSION['email'],
        "role" => $_SESSION['role'],
        "barangay" => $_SESSION['barangay']
    ]
]);
