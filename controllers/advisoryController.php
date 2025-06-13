<?php


require_once "../config/db.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$db = new Database();
$pdo = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(["error" => "Only POST requests are allowed."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$type = $_GET['type'] ?? '';

if ($type === 'weather') {
    $stmt = $pdo->prepare("INSERT INTO weather_advisories (title, details, date_time) VALUES (?, ?, ?)");
    $stmt->execute([$data['title'], $data['details'], $data['dateTime']]);

    http_response_code(201); 
    echo json_encode([
        "message" => "Weather advisory posted.",
        "timestamp" => date("Y-m-d H:i:s")
    ]);

} elseif ($type === 'road') {
    $stmt = $pdo->prepare("INSERT INTO road_advisories (title, details, date_time, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['status']]);

    http_response_code(201); 
    echo json_encode([
        "message" => "Road advisory posted.",
        "timestamp" => date("Y-m-d H:i:s")
    ]);

} elseif ($type === 'disaster') {
    $stmt = $pdo->prepare("INSERT INTO Disaster_update (img_path, title, details, date_time, disaster_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['img_path'],
        $data['title'],
        $data['details'],
        $data['dateTime'],
        $data['disaster_type']
    ]);

    http_response_code(201); 
    echo json_encode([
        "message" => "Disaster update posted.",
        "timestamp" => date("Y-m-d H:i:s")
    ]);


} elseif ($type === 'community') {
    $stmt = $pdo->prepare("INSERT INTO community_notice (title, details, date_time) VALUES (?, ?, ?)");
    $stmt->execute([
        $data['title'],
        $data['details'],
        $data['dateTime']
    ]);

    http_response_code(201); 
    echo json_encode([
        "message" => "Community notice posted.",
        "timestamp" => date("Y-m-d H:i:s")
    ]);


} else {
    http_response_code(400); 
    echo json_encode(["error" => "Invalid advisory type."]);
}
