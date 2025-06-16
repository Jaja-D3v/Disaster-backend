<?php
require_once "../config/db.php";
require_once "../models/Advisory.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["error" => "Only PUT requests are allowed."]);
    exit();
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);

if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or missing ID."]);
    exit();
}

$db = new Database();
$pdo = $db->connect();
$advisory = new Advisory($pdo);

$result = false;

switch ($type) {
    case 'weather':
        $result = $advisory->updateWeather($id, $data);
        $response["message"] = "Weather advisory updated.";
        break;
    case 'road':
        $result = $advisory->updateRoad($id, $data);
        $response["message"] = "Road advisory updated.";
        break;
    case 'disaster':
        $result = $advisory->updateDisaster($id, $data);
        $response["message"] = "Disaster update updated.";
        break;
    case 'community':
        $result = $advisory->updateCommunity($id, $data);
        $response["message"] = "Community notice updated.";
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid advisory type."]);
        exit();
}

if ($result === false) {
    http_response_code(404);
    echo json_encode(["error" => "Record with ID $id not found."]);
    exit();
}

http_response_code(200);
echo json_encode($response);