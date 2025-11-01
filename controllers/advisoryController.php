<?php


require_once "../config/db.php";
require_once "../models/Advisory.php";
require_once "../config/auth.php";

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please log in."]);
    exit();
}

$type = $_GET['type'] ?? '';

$db = new Database();
$pdo = $db->connect();
$advisory = new Advisory($pdo);

$response = ["timestamp" => date("Y-m-d H:i:s")];

switch ($type) {
    case 'weather':
    case 'road':
    case 'community':
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Invalid JSON input."]);
            exit();
        }

        $data['added_by'] = $userId;

        if ($type === 'weather') {
            $success = $advisory->createWeather($data);
            $response["message"] = "Weather advisory posted.";
        } elseif ($type === 'road') {
            $success = $advisory->createRoad($data);
            $response["message"] = "Road advisory posted.";
        } else {
            $success = $advisory->createCommunity($data);
            $response["message"] = "Community notice posted.";
        }

        if (!$success) {
            http_response_code(500);
            $response["error"] = "Failed to create $type advisory.";
        }
        break;

    case 'disaster':
        $title = $_POST['title'] ?? '';
        $details = $_POST['details'] ?? '';
        $dateTime = $_POST['dateTime'] ?? '';
        $disasterType = $_POST['disasterType'] ?? '';
        $imagePath = null;

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
            'added_by' => $userId
        ];

        if ($advisory->createDisaster($data)) {
            $response["message"] = "Disaster update posted.";
        } else {
            http_response_code(500);
            $response["error"] = "Failed to create disaster update.";
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(["error" => "Invalid advisory type."]);
        exit();
}

http_response_code(201);
echo json_encode($response);
