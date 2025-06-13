<?php


require_once "../config/db.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$db = new Database();
$pdo = $db->connect();

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'weather':
        $stmt = $pdo->query("SELECT * FROM weather_advisories ORDER BY date_time DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'road':
        $stmt = $pdo->query("SELECT * FROM road_advisories ORDER BY date_time DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'disaster':
        $stmt = $pdo->query("SELECT * FROM Disaster_update ORDER BY date_time DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'community':
        $stmt = $pdo->query("SELECT * FROM community_notice ORDER BY date_time DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    default:
        http_response_code(400); 
        echo json_encode(["error" => "Invalid or missing advisory type."]);
        break;
}
