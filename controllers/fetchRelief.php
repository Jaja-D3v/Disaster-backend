<?php
require_once "../config/db.php";
require_once "../models/ReliefPack.php";

header("Content-Type: application/json");

try {
    $db = new Database();
    $pdo = $db->connect();

    $reliefPackModel = new ReliefPack($pdo);

    
    $relief_packs = $reliefPackModel->fetchAll();

    echo json_encode([
        "success" => true,
        "relief_packs" => $relief_packs
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
