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
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $details = $_POST['details'] ?? '';
        $dateTime = $_POST['dateTime'] ?? '';
        $disasterType = $_POST['disasterType'] ?? '';
        $imagePath = null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing ID for update."]);
            exit();
        }

        // Optional: Fetch existing image to delete if replacing
        $existing = $advisory->findDisasterById($id);
        $oldImagePath = $existing['img_path'] ?? null;

        // Check for new uploaded image
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

            // Delete old image if new one is uploaded
            if ($oldImagePath) {
                $oldFullPath = __DIR__ . '/../' . $oldImagePath;
                if (file_exists($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }

            $imagePath = 'uploads/disasterPost/' . $imageName;
        } else {
            $imagePath = $oldImagePath; // Keep old image if none uploaded
        }

        // Build updated data
        $data = [
            'title' => $title,
            'details' => $details,
            'dateTime' => $dateTime,
            'disasterType' => $disasterType,
            'image' => $imagePath
        ];

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