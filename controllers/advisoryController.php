<?php
require_once "../config/db.php";
require_once "../models/Advisory.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST requests are allowed."]);
    exit();
}

$type = $_GET['type'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

$db = new Database();
$pdo = $db->connect();
$advisory = new Advisory($pdo);

$response = ["timestamp" => date("Y-m-d H:i:s")];

switch ($type) {
    case 'weather':
        $advisory->createWeather($data);
        $response["message"] = "Weather advisory posted.";
        break;
    case 'road':
        $advisory->createRoad($data);
        $response["message"] = "Road advisory posted.";
        break;
    case 'disaster':
        $advisory->createDisaster($data);
        $response["message"] = "Disaster update posted.";
        break;
    case 'community':
        $advisory->createCommunity($data);
        $response["message"] = "Community notice posted.";
        break;
    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid advisory type."]);
        exit();
}

http_response_code(201);
echo json_encode($response);
