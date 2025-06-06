<?php
require_once "../config/db.php";
require_once "../models/User.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
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

if ($user && $user["password"] === $password) {
    echo json_encode([
        "success" => true,
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
