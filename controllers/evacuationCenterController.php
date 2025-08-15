<?php
require_once "../config/db.php";
require_once "../models/evacuationCenter.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


$method = $_SERVER['REQUEST_METHOD'];
$override = $_POST['_method'] ?? json_decode(file_get_contents("php://input"), true)['_method'] ?? null;

$db = new Database();
$pdo = $db->connect();
$evacuation = new EvacuationCenter($pdo);

switch (true) {
    case $method === 'GET':
        $id = $_GET['id'] ?? null;

        if ($id && is_numeric($id)) {
            $result = $evacuation->getById($id);
        } else {
            $result = $evacuation->getAll();
        }

        echo json_encode($result);
        break;

    case $method === 'POST' && !$override:
        $data = json_decode(file_get_contents("php://input"), true);
        $saved = $evacuation->add($data);

        if ($saved) {
            echo json_encode(["success" => true, "message" => "Evacuation center added successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add evacuation center."]);
        }
        break;

    case $method === 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing or invalid ID."]);
            exit();
        }

        $updated = $evacuation->update($id, $data);

        if ($updated) {
            echo json_encode(["success" => true, "message" => "Evacuation center updated."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Update failed."]);
        }
        break;

    case $method === 'POST' && $override === 'DELETE':
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing or invalid ID."]);
            exit();
        }

        $deleted = $evacuation->delete($id);

        if ($deleted) {
            echo json_encode(["success" => true, "message" => "Evacuation center deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete evacuation center."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Unsupported or missing _method override."]);
        break;
}
