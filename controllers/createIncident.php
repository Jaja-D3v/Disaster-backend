<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';

// Create database connection
$db = new Database();
$pdo = $db->connect();

$incidentModel = new Incident($pdo);

$reporter_name = $_POST['reporter_name'] ?? null;
$reporter_contact = $_POST['reporter_contact'] ?? null;
$description = $_POST['description'] ?? null;

if (!$reporter_contact || !$description) {
    echo json_encode(['error' => 'Contact and description are required']);
    exit;
}

// Handle file upload (single file)
$media_path = null;
if (!empty($_FILES['media']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/incidentPhotos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['media']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
        // Save relative path to DB
        $media_path = 'incidentPhotos/' . $fileName;
    } else {
        echo json_encode(['error' => 'Failed to upload file']);
        exit;
    }
}

// Insert into DB
$id = $incidentModel->createIncident($reporter_name, $reporter_contact, $description, $media_path);

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
