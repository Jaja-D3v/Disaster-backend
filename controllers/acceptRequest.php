<?php
require_once "../config/db.php"; 
require_once "../config/auth.php"; 
require_once "../models/User.php"; 
require_once "../utils/sendEmail.php";

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); 
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->connect();
    $userModel = new User($pdo);
    $requestData = json_decode(file_get_contents("php://input"), true);
    $pendingId = $requestData['id'] ?? null; 
    $password = $requestData['password'] ?? null; 

    if (!$pendingId || !$password) {
        throw new Exception("Pending ID and password are required");
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    $loggedUserId = $_SESSION['user_id'];
    $user = $userModel->getById($loggedUserId);

    if (!$user) throw new Exception("Logged-in user not found");
    if (!password_verify($password, $user['password'])) throw new Exception("Incorrect password");
    if ($user['role'] != 1) throw new Exception("User is not authorized to approve pending accounts");

    $pendingUser = $userModel->getPendingById($pendingId);
    if (!$pendingUser) throw new Exception("Pending account not found");

    $userModel->approvePending($pendingUser);
    $userModel->deletePendingRequest($pendingId);

    
    $email = $pendingUser['email'];
    $username = $pendingUser['username'];
    $body = "Hi {$username},<br><br>
    We are pleased to inform you that your account has been <b>approved</b> and is now <b>active</b>. 
    You can now <b>log in</b> using your registered credentials.<br><br>
    Welcome aboard!<br>
    Best regards,<br>
    Disaster Ready Team Administration";

    $result = sendEmail($email, "Disaster Ready - Your Account Has Been Approved", $body);

    echo json_encode([
        "success" => true,
        "message" => "Pending account approved and moved to users table"
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
