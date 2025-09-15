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

$db = new Database();
$pdo = $db->connect();
$advisory = new Advisory($pdo);

$response = ["timestamp" => date("Y-m-d H:i:s")];

switch ($type) {
    case 'weather':
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['added_by'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing added_by field."]);
        exit();
    }
    if ($advisory->createWeather($data)) {
        $response["message"] = "Weather advisory posted.";
    } else {
        http_response_code(500);
        $response["error"] = "Failed to create weather advisory.";
    }
    break;

case 'road':
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['added_by'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing added_by field."]);
        exit();
    }
    if ($advisory->createRoad($data)) {
        $response["message"] = "Road advisory posted.";
    } else {
        http_response_code(500);
        $response["error"] = "Failed to create road advisory.";
    }
    break;

case 'disaster':
    $title = $_POST['title'] ?? '';
    $details = $_POST['details'] ?? '';
    $dateTime = $_POST['dateTime'] ?? '';
    $disasterType = $_POST['disasterType'] ?? '';
    $addedBy = $_POST['added_by'] ?? null;
    $imagePath = null;

    if (!$addedBy) {
        http_response_code(400);
        echo json_encode(["error" => "Missing added_by field."]);
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/disasterPost/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        if (!move_uploaded_file($imageTmp, $imagePath)) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to upload image."]);
            exit();
        }
        $imagePath = 'uploads/disasterPost/' . $imageName;
    }

    $data = [
        'title' => $title,
        'details' => $details,
        'dateTime' => $dateTime,
        'disasterType' => $disasterType,
        'image' => $imagePath,
        'added_by' => $addedBy
    ];

    if ($advisory->createDisaster($data)) {
        $response["message"] = "Disaster update posted.";
    } else {
        http_response_code(500);
        $response["error"] = "Failed to create disaster update.";
    }
    break;

case 'community':
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['added_by'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing added_by field."]);
        exit();
    }
    if ($advisory->createCommunity($data)) {
        $response["message"] = "Community notice posted.";
    } else {
        http_response_code(500);
        $response["error"] = "Failed to create community notice.";
    }
    break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid advisory type."]);
        exit();
}

http_response_code(201);
echo json_encode($response);
