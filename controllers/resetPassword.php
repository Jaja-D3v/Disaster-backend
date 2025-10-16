<?php
require_once "../models/resetAccount.php";
date_default_timezone_set('Asia/Manila');
$data = json_decode(file_get_contents("php://input"), true);

$userModel = new User();
$Ntoken = $_GET['token'] ?? '';

if (!$Ntoken) {
    die("Invalid request.");
}

$reset = $userModel->validateToken($Ntoken);

if (!$reset) {
    die("Token is invalid or expired.");
}

// pang validation ng token
echo "Token valid! Username: " . $reset['email'];



$token = trim($data['token'] ?? '');
$newPassword = trim($data['password'] ?? '');

if (!$token || !$newPassword) {
    echo json_encode([
        "success" => false,
        "message" => "Token and new password are required."
    ]);
    exit;
}

$userModel = new User();

// pang validate ulit ng token
$reset = $userModel->validateToken($token);

if (!$reset) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid or expired token."
    ]);
    exit;
}


$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$userModel->updatePassword($reset['email'], $hashedPassword);


$userModel->deleteResetTokenByEmail($reset['email']);

echo json_encode([
    "success" => true,
    "message" => "Password has been successfully reset."
]);