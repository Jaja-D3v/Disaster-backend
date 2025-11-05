<?php
require_once "../config/db.php";
require_once "../models/Client.php";

$db = new Database();
$pdo = $db->connect();
$roadAdvisoriesModel = new Client($pdo);
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $roadAdvisories = $roadAdvisoriesModel->getAllRoadAdvisories(); 
        echo json_encode([
            "success" => true,
            "data" => $roadAdvisories
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
