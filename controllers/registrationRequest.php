<?php
require_once "../config/db.php";
require_once "../utils/sendEmail.php";
require_once "../models/User.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$email = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");
$confirm_password = trim($data["confirm_password"] ?? "");
$barangay = trim($data["barangay"] ?? "");

// Validate required fields
if ($username === "" || $email === "" || $password === "" || $confirm_password === "") {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Confirm password
if ($password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "Password and confirm password do not match."]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit;
}

// Validate password strength
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d_+!@#$%^&*()-=]{8,}$/', $password)) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters and include letters and numbers."
    ]);
    exit;
}

// Connect DB
$db = (new Database())->connect();
$userModel = new User($db);

// Check total registered accounts
$totalAccounts = $userModel->countUsers();
if ($totalAccounts >= 25) {
    echo json_encode(["success" => false, "message" => "Maximum number of accounts (25) reached."]);
    exit;
}

// Check if username already exists
if ($userModel->findByUsername($username)) {
    echo json_encode(["success" => false, "message" => "Username already exists."]);
    exit;
}

// Check if email already exists in users table
if ($userModel->findByEmail($email)) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    exit;
}

// Generate verification code
$code = rand(100000, 999999);
$expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Store pending registration (full info + code)
$userModel->createPending(
    $username,
    $email,
    password_hash($password, PASSWORD_DEFAULT),
    $barangay,
    $code,
    $expires
);

// Send verification email
$body = "Hi {$username},<br>Your verification code is: <b>{$code}</b>. It expires in 5 minutes.";
$result = sendEmail($email, "Disaster Ready - Email Verification", $body);

echo json_encode($result);
