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
$barangay = trim($data["barangay"] ?? "");

if ($username === "" || $email === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}


if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d_+!@#$%^&*()-=]{8,}$/', $password)) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters and include both letters and numbers. Special characters are allowed."
    ]);
    exit;
}


$db = (new Database())->connect();
$userModel = new User($db);

// Check if username exists
if ($userModel->findByUsername($username)) {
    echo json_encode(["success" => false, "message" => "Username already exists."]);
    exit;
}

// Check if email exists
if ($userModel->findByEmail($email)) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Automatically set role to 2 (regular user)
$created = $userModel->createUser($username, $email, $hashedPassword, 2, $barangay);

if ($created) {
    echo json_encode(["success" => true, "message" => "User registered successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to register user."]);
}
