<?php
require_once "../models/User.php";
require_once "../config/db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? "");
$codeInput = trim($data['code'] ?? "");

$db = (new Database())->connect();
$userModel = new User($db);

$pending = $userModel->getPending($email);

if (!$pending) {
    echo json_encode(["success" => false, "message" => "No pending registration found."]);
    exit;
}

// Verify expiry first
if (strtotime($pending['expires']) < time()) {
    $userModel->deletePending($pending['id']);
    echo json_encode(["success" => false, "message" => "Verification code expired. Please register again."]);
    exit;
}

// Verify code
if ($pending['code'] !== $codeInput) {
    // Increment attempts
    $stmt = $db->prepare("UPDATE pending_registrations SET attempts = attempts + 1 WHERE id = ?");
    $stmt->execute([$pending['id']]);

    $currentAttempts = $pending['attempts'] + 1; // new attempt count

    if ($currentAttempts >= 4) {
        // Cancel registration after 4 failed attempts
        $userModel->deletePending($pending['id']);
        echo json_encode(["success" => false, "message" => "Incorrect code. You have reached 4 attempts. Registration canceled."]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "Incorrect code. Attempt {$currentAttempts} of 4."]);
    exit;
}

// Create user
$created = $userModel->createUser(
    $pending['username'],
    $pending['email'],
    $pending['password'],
    2, // role
    $pending['barangay']
);

// Delete pending registration after successful creation
$userModel->deletePending($pending['id']);

if ($created) {
    echo json_encode(["success" => true, "message" => "User registered successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to create user."]);
}
