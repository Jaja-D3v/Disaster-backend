<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$recaptchaSecret = $_ENV['RECAPTCHA_SECRET'] ?? null;
if (!$recaptchaSecret) {
    echo json_encode(['error' => 'reCAPTCHA secret key not found']);
    exit;
}

$captcha = $_POST['g-recaptcha-response'] ?? null;
if (!$captcha) {
    echo json_encode(['error' => 'Captcha missing']);
    exit;
}


$verify = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$captcha"
);
$response = json_decode($verify);

if (!$response->success) {
    echo json_encode(['error' => 'Captcha verification failed']);
    exit;
}

$db = new Database();
$pdo = $db->connect();
$incidentModel = new Incident($pdo);

$reporter_name = $_POST['reporter_name'] ?? null;
$reporter_contact = $_POST['reporter_contact'] ?? null;
$description = $_POST['description'] ?? null;
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;
$severity = $_POST['severity'] ?? null;

if (!$reporter_contact || !$description) {
    echo json_encode(['error' => 'Contact and description are required']);
    exit;
}

$media_path = null;
if (!empty($_FILES['media']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/incidentPhotos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['media']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
        $media_path = 'incidentPhotos/' . $fileName;
    } else {
        echo json_encode(['error' => 'Failed to upload file']);
        exit;
    }
}

$id = $incidentModel->createIncident(
    $reporter_name,
    $reporter_contact,
    $description,
    $lat,
    $lng,
    $severity,
    $media_path
);

if ($id) {
    echo json_encode([
        'success' => true,
        'message' => 'Incident reported successfully',
        'id' => $id,
        'media' => $media_path
    ]);
} else {
    echo json_encode(['error' => 'Failed to insert incident']);
}
