<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); //1
ini_set('session.cookie_samesite', 'Strict');
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'httponly' => true,
    'secure' => false, //isset($_SERVER['HTTPS'])
    'samesite' => 'Lax' //Strict
]);
session_start();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    session_unset();
    session_destroy();
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Session expired. Please log in again."]);
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}
