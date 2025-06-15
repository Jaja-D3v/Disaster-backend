<?php

require_once "../config/db.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$db = new Database();
$pdo = $db->connect();

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? null;

$tableMap = [
    'weather' => 'weather_advisories',
    'road' => 'road_advisories',
    'disaster' => 'Disaster_update',
    'community' => 'community_notice',
];

if (!isset($tableMap[$type])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing advisory type."]);
    exit;
}

$table = $tableMap[$type];

try {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT * FROM $table ORDER BY date_time DESC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($result ?: ["message" => "No records found."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
