<?php
// ✅ Allow only your frontend to access
header("Access-Control-Allow-Origin: https://your-frontend-domain.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../config/auth.php"; // ensures only logged-in users can log out

// ----------------------
// Destroy session safely
// ----------------------
// Unset all session variables
$_SESSION = [];

// Destroy session cookie securely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),    // name
        '',                // value
        time() - 42000,    // expire time
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? true
    );
}


// Finally destroy the session
session_destroy();

// Optional: also update user status in DB if you’re tracking online/offline
/*
require_once "../config/db.php";
require_once "../models/User.php";
$db = (new Database())->connect();
$userModel = new User($db);
$userModel->updateUserStatus($_SESSION['user_id'], 'inactive');
*/

echo json_encode([
    "success" => true,
    "message" => "You have been logged out securely."
]);
?>
