<?php
require_once "../config/db.php";
require_once "../models/reliefPack.php";

header("Content-Type: application/json");

try {
    $db = new Database();
    $pdo = $db->connect();

    $barangayReceivedPacks = new ReliefPack($pdo);

    
    $listOfBarangays = $barangayReceivedPacks->getAllBarangaysReceived();

    echo json_encode([
        "success" => true,
        "listOfBarangays" => $listOfBarangays
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
