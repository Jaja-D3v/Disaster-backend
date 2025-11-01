<?php
require_once "../config/db.php";
require_once "../models/ReliefPack.php";

header("Content-Type: application/json");

$db = new Database();
$pdo = $db->connect();
$reliefPackModel = new ReliefPack($pdo);

try {
    $input = json_decode(file_get_contents("php://input"), true);

    $pdo->beginTransaction();

    
    if (isset($input['description'], $input['total_packs'], $input['date_input'])) {
        $relief_pack_id = $reliefPackModel->insert(
            $input['description'],
            intval($input['total_packs']),
            $input['date_input']
        );

        $pdo->commit();
        echo json_encode([
            'success' => true,
            'relief_pack_id' => $relief_pack_id,
            'message' => 'Relief pack inserted successfully'
        ]);
        exit;
    }

    
    if (!isset($input['relief_pack_id'], $input['selected_barangays'], $input['allocation_mode'])) {
        throw new Exception("Missing required fields for distribution");
    }

    $result = $reliefPackModel->distribute(
        intval($input['relief_pack_id']),
        $input['selected_barangays'],
        strtolower($input['allocation_mode']),
        $input['based_on'] ?? null,
        $input['manual_allocations'] ?? null
    );

    $pdo->commit();
    echo json_encode([
        'success' => true,
        'relief_pack_id' => $result['relief_pack_id'],
        'allocated_packs' => $result['allocated_packs'],
        'remaining_packs' => $result['remaining_packs']
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
