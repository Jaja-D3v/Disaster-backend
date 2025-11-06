<?php
require_once "../config/db.php";
require_once "../config/auth.php";
require_once "../models/Advisory.php";
require_once "../helpers/advisoryHelpers.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Only POST requests are allowed."]);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$barangayname =  $_SESSION['barangay'];
if (!$barangayname) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please log in."]);
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
$response = [];

switch ($type) {
    case 'weather':
        $data['added_by'] = $barangayname; 
        $result = $advisory->updateWeather($id, $data);
        $response["message"] = "Weather advisory updated.";
        break;

    case 'road':
        $data['added_by'] = $barangayname; 
        $result = $advisory->updateRoad($id, $data);
        $response["message"] = "Road advisory updated.";
        break;

    case 'disaster':
        
        $existing = $advisory->findDisasterById($id);
        $oldImagePath = $existing['img_path'] ?? null;

        $title = $_POST['title'] ?? '';
        $details = $_POST['details'] ?? '';
        $dateTime = $_POST['dateTime'] ?? '';
        $disasterType = $_POST['disasterType'] ?? '';

        $imagePath = $oldImagePath;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/disasterPost/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;

            if (!move_uploaded_file($imageTmp, $imagePath)) {
                http_response_code(500);
                echo json_encode(["error" => "Failed to upload image."]);
                exit();
            }

            if ($oldImagePath) deleteDisasterImage($oldImagePath);
            $imagePath = 'uploads/disasterPost/' . $imageName;
        }
        
        $data = [
            'title' => $title,
            'details' => $details,
            'dateTime' => $dateTime,
            'disasterType' => $disasterType,
            'image' => $imagePath
        ];
        $data['added_by'] = $barangayname; 
        $result = $advisory->updateDisaster($id, $data);
        $response["message"] = "Disaster update updated.";
        break;

    case 'community':
        $data['added_by'] = $barangayname; 
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
