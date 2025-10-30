<?php
require_once "../config/db.php";
require_once "../models/User.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$db = new Database();
$pdo = $db->connect();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $users = $userModel->getAllUsers(); // excludes passwords
        echo json_encode([
            "success" => true,
            "data" => $users
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
}
