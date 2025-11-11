<?php
require_once "../config/db.php";
require_once "../models/Client.php";

$db = new Database();
$pdo = $db->connect();
$disasterMappingModel = new Client($pdo);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = (int)$_GET['id']; 
            $disasterMapping = $disasterMappingModel->getAllDisasterMappingById($id);
        } else {
            $disasterMapping = $disasterMappingModel->getAllDisasterMapping();
        }

        echo json_encode([
            "success" => true,
            "data" => $disasterMapping
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

