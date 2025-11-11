<?php
require_once "../config/db.php";
require_once "../models/Client.php";

$db = new Database();
$pdo = $db->connect();
$barangayContactInfoModel = new Client($pdo);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = (int)$_GET['id']; 
            $barangayContactInfo = $barangayContactInfoModel->getAllBarangayContactInfoById($id);
        } else {
            $barangayContactInfo = $barangayContactInfoModel->getAllBarangayContactInfo();
        }

        echo json_encode([
            "success" => true,
            "data" => $barangayContactInfo
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

