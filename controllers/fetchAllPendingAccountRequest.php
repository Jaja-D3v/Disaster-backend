<?php
require_once "../config/db.php"; 
require_once "../models/User.php"; 

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); 
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit;
}
try {
    
    $db = new Database();
    $pdo = $db->connect();
    $pendingModel = new User($pdo);
    $pendingAccounts = $pendingModel->fetchAllPending();

    echo json_encode([
        "success" => true,
        "data" => $pendingAccounts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
