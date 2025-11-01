<?php
require_once "../models/resetAccount.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["token"], $data["password"], $data["confirmPassword"])) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

$password = $data["password"];
$confirmPassword = $data["confirmPassword"];

if ($password !== $confirmPassword) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters."]);
    exit;
}

$userModel = new User();
$response = $userModel->resetPasswordWithToken($data["token"], $password, $confirmPassword);

echo json_encode($response);
