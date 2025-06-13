<?php
require_once "../config/db.php";
require_once "../models/User.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Only POST requests allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$email = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");
$role = trim($data["role"] ?? "user"); // optional default role
$barangay = trim($data["barangay"] ?? ""); // optional

if ($username === "" || $email === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

$db = (new Database())->connect();
$userModel = new User($db);


if ($userModel->findByUsername($username)) {
    echo json_encode(["success" => false, "message" => "Username already exists."]);
    exit;
}


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$created = $userModel->createUser($username, $email, $hashedPassword, $role, $barangay);

if ($created) {
    echo json_encode(["success" => true, "message" => "User registered successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to register user."]);
}
