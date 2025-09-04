<?php
require_once "../config/db.php";
require_once "../models/Account.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Create database connection
$database = new Database();
$pdo = $database->connect();

$account = new Account($pdo);

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'])) {
    echo json_encode(["success" => false, "message" => "No action specified."]);
    exit;
}

$userId = $data['id'] ?? null;

switch ($data['action']) {
    case "updateProfile":
        if (!isset($data['username'], $data['email'], $data['password'])) {
            echo json_encode(["success" => false, "message" => "Missing fields."]);
            exit;
        }
        echo json_encode($account->updateProfile(
            $userId,
            $data['username'],
            $data['email'],
            $data['password'] 
        ));
        break;

    case "changePassword":
    if (!isset($data['currentPassword'], $data['newPassword'], $data['confirmPassword'])) {
        echo json_encode(["success" => false, "message" => "Missing fields."]);
        exit;
    }
    echo json_encode($account->changePassword(
        $userId,
        $data['currentPassword'],
        $data['newPassword'],
        $data['confirmPassword']
    ));
    break;


    default:
        echo json_encode(["success" => false, "message" => "Invalid action."]);
}
