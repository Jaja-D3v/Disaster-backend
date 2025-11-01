<?php
require_once "../config/db.php";
require_once "../models/Advisory.php";
require_once "../helpers/advisoryHelpers.php";

$db = new Database();
$pdo = $db->connect();
$advisory = new Advisory($pdo);

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
        switch ($type) {
            case 'weather':
                $result = $advisory->findWeatherById($id);
                break;
            case 'road':
                $result = $advisory->findRoadById($id);
                break;
            case 'disaster':
                $result = $advisory->findDisasterById($id);
                $result = addBaseUrl($result);
                break;
            case 'community':
                $result = $advisory->findCommunityById($id);
                break;
        }
    } else {
        $result = $advisory->getAll($table);
        if ($type === 'disaster') {
            foreach ($result as &$row) {
                $row = addBaseUrl($row);
            }
        }
    }

    echo json_encode($result ?: ["message" => "No records found."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
