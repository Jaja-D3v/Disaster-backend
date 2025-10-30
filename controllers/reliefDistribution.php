<?php
require_once "../config/db.php";

header("Content-Type: application/json");

// Connect to DB
$db = new Database();
$pdo = $db->connect();
$pdo->beginTransaction();

try {
    $input = json_decode(file_get_contents("php://input"), true);

    // Insert a new relief pack
    if (isset($input['description'], $input['total_packs'], $input['date_input'])) {
        $description = $input['description'];
        $total_packs = intval($input['total_packs']);
        $date_input = $input['date_input'];

        if ($total_packs <= 0) {
            throw new Exception("Total packs must be greater than zero");
        }

        $stmt = $pdo->prepare("INSERT INTO relief_packs (description, total_packs, date_input, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$description, $total_packs, $date_input]);
        $relief_pack_id = $pdo->lastInsertId();

        $pdo->commit();
        echo json_encode([
            "success" => true,
            "relief_pack_id" => $relief_pack_id,
            "message" => "Relief pack inserted successfully"
        ]);
        exit;
    }

    // Distribute existing relief pack
    if (!isset($input['relief_pack_id'], $input['selected_barangays'], $input['allocation_mode'])) {
        throw new Exception("Missing required fields for distribution");
    }

    $relief_pack_id = intval($input['relief_pack_id']);
    $selected_barangays = $input['selected_barangays'];
    $allocation_mode = strtolower($input['allocation_mode']);

    if (empty($selected_barangays)) {
        throw new Exception("At least one barangay must be selected");
    }

    $stmt = $pdo->prepare("SELECT * FROM relief_packs WHERE id = ?");
    $stmt->execute([$relief_pack_id]);
    $relief_pack = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$relief_pack) {
        throw new Exception("Relief pack not found");
    }

    $total_packs_available = intval($relief_pack['total_packs']);
    if ($total_packs_available <= 0) {
        $stmt = $pdo->prepare("DELETE FROM relief_packs WHERE id = ?");
        $stmt->execute([$relief_pack_id]);
        throw new Exception("Relief pack is empty and deleted");
    }

    $placeholders = implode(',', array_fill(0, count($selected_barangays), '?'));
    $stmt = $pdo->prepare("SELECT id, barangay_name, total_male, total_female, total_families FROM barangay_contact_info WHERE id IN ($placeholders)");
    $stmt->execute($selected_barangays);
    $barangays_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($barangays_info)) {
        throw new Exception("No valid barangays found");
    }

    $allocations = [];
    if ($allocation_mode === 'manual') {
        if (!isset($input['manual_allocations']) || !is_array($input['manual_allocations'])) {
            throw new Exception("Manual allocations missing");
        }
        $sum = array_sum($input['manual_allocations']);
        if ($sum > $total_packs_available) {
            throw new Exception("Sum of manual allocations exceeds available packs");
        }
        $allocations = $input['manual_allocations'];
    } else {
        if (!isset($input['based_on']) || !in_array($input['based_on'], ['population','families'])) {
            throw new Exception("Automatic allocation requires 'based_on' parameter (population or families).");
        }

        $based_on = $input['based_on'];
        $total_count = 0;
        $barangays_info_map = [];
        foreach ($barangays_info as $b) {
            $count = ($based_on === 'population') ? ($b['total_male'] + $b['total_female']) : $b['total_families'];
            $barangays_info_map[$b['id']] = $count;
            $total_count += $count;
        }

        if ($total_packs_available < $total_count) {
            throw new Exception("Insufficient relief packs. Required: $total_count, Available: $total_packs_available");
        }

        foreach ($barangays_info_map as $b_id => $count) {
            $allocations[$b_id] = floor(($count / $total_count) * $total_packs_available);
        }

        $sum_allocated = array_sum($allocations);
        $remainder = $total_packs_available - $sum_allocated;
        foreach ($barangays_info_map as $b_id => $count) {
            if ($remainder <= 0) break;
            $allocations[$b_id] += 1;
            $remainder--;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO relief_pack_barangays (relief_pack_id, barangay_id, allocated_packs) VALUES (?, ?, ?)");
    $total_allocated = 0;
    foreach ($allocations as $barangay_id => $packs) {
        $stmt->execute([$relief_pack_id, $barangay_id, $packs]);
        $total_allocated += $packs;
    }

    $remaining_packs = $total_packs_available - $total_allocated;
    if ($remaining_packs <= 0) {
        $stmt = $pdo->prepare("DELETE FROM relief_packs WHERE id = ?");
        $stmt->execute([$relief_pack_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE relief_packs SET total_packs = ? WHERE id = ?");
        $stmt->execute([$remaining_packs, $relief_pack_id]);
    }

    $pdo->commit();
    echo json_encode([
        "success" => true,
        "relief_pack_id" => $relief_pack_id,
        "allocated_packs" => $allocations,
        "remaining_packs" => $remaining_packs
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
