<?php
require_once "../config/db.php";
require_once "../models/User.php";
require_once "../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secret_key = "your_super_secret_key"; // change this to something private
$issuedAt = time();
$expire = $issuedAt + (60 * 60); // 1 hour token validity

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(200);
    echo json_encode(["success" => false, "message" => "Only POST requests allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if ($username === "" || $password === "") {
    http_response_code(200);
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit;
}

$db = (new Database())->connect();
$userModel = new User($db);
$user = $userModel->findByUsername($username);


if ($user && password_verify($password, $user["password"])) {
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
    http_response_code(200);
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
}
