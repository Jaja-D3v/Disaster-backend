<?php
require_once "../config/db.php";
require_once "../models/Advisory.php";
require_once "../helpers/advisoryHelpers.php";

$method = $_SERVER['REQUEST_METHOD'];
$override = $_POST['_method'] ?? json_decode(file_get_contents("php://input"), true)['_method'] ?? null;

if ($method === 'POST' && $override === 'DELETE') {
    $type = $_GET['type'] ?? '';
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing or invalid ID."]);
        exit();
    }

    $db = new Database();
    $pdo = $db->connect();
    $advisory = new Advisory($pdo);

    $exists = false;
    $deleted = false;

    switch ($type) {
        case 'weather':
            $exists = $advisory->findWeatherById($id);
            if ($exists) $deleted = $advisory->deleteWeather($id);
            break;
        case 'road':
            $exists = $advisory->findRoadById($id);
            if ($exists) $deleted = $advisory->deleteRoad($id);
            break;
        case 'disaster':
            $exists = $advisory->findDisasterById($id);
            if ($exists) {
                if (isset($exists['img_path'])) deleteDisasterImage($exists['img_path']);
                $deleted = $advisory->deleteDisaster($id);
            }
            break;
        case 'community':
            $exists = $advisory->findCommunityById($id);
            if ($exists) $deleted = $advisory->deleteCommunity($id);
            break;
        default:
            http_response_code(400);
            echo json_encode(["error" => "Invalid advisory type."]);
            exit();
    }

    if (!$exists) {
        http_response_code(404);
        echo json_encode(["error" => "Record not found."]);
        exit();
    }

    if ($deleted) {
        echo json_encode(["success" => true, "message" => "Deleted successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Missing or invalid _method override."]);
}
