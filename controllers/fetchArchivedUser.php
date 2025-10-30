<?php
require_once "../config/db.php";
require_once "../models/User.php";

$db = new Database();
$pdo = $db->connect();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $archivedUsers = $userModel->getAllArchivedUsers(); 
        echo json_encode([
            "success" => true,
            "data" => $archivedUsers
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
