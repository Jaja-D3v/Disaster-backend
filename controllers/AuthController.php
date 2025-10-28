<?php
require_once "../config/db.php";
require_once "../models/User.php";
require_once "../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your_super_secret_key"; 
$issuedAt = time();
$expire = $issuedAt + (60 * 60); // 1 hour

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

date_default_timezone_set('Asia/Manila');


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Only POST requests allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if ($username === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit;
}

$db = (new Database())->connect();
$userModel = new User($db);
$user = $userModel->findByUsername($username);

// Check attempts before verifying password
$attemptData = $userModel->getUserAttempts($username);

if ($attemptData) {
    $attempts = $attemptData["login_attempts"];
    $lastAttempt = $attemptData["last_attempt_at"];
    $cooldownMinutes = 5;

    if ($attempts >= 4 && $lastAttempt) {
        $lastAttemptTime = strtotime($lastAttempt);
        $timeDiff = time() - $lastAttemptTime;

        if ($timeDiff < $cooldownMinutes * 60) {
            $remaining = ceil(($cooldownMinutes * 60 - $timeDiff) / 60);
            echo json_encode([
                "success" => false,
                "message" => "Too many failed attempts. Please try again after {$remaining} minute(s)."
            ]);
            exit;
        } else {
            // Reset after cooldown
            $userModel->resetLoginAttempts($username);
        }
    }
}

if ($user && password_verify($password, $user["password"])) {
    // Reset attempts after successful login
    $userModel->resetLoginAttempts($username);

    $payload = [
        "iss" => "http://localhost", 
        "iat" => $issuedAt,
        "exp" => $expire,
        "data" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"]
        ]
    ];

    $userModel->updateLastLogin($user['id']); 
    $userModel->updateUserStatus($user['id'], 'active');

    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    echo json_encode([
        "success" => true,
        "token" => $jwt,
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "barangay" => $user["barangay"]
        ]
    ]);
} else {
    // Increase failed attempt count
    $userModel->incrementLoginAttempts($username);
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
}
