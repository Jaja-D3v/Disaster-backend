<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';

$db = new Database();
$pdo = $db->connect();
$incidentModel = new Incident($pdo);

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? $_POST['id'] ?? null;
$status = $data['status'] ?? $_POST['status'] ?? null;
$responded_by = $data['responded_by'] ?? $_POST['responded_by'] ?? null;

if (!$id || !$status) {
    echo json_encode(['error' => 'Incident ID and status are required']);
    exit;
}

// Fetch reporter contact before updating
$stmt = $pdo->prepare("SELECT reporter_contact FROM incident_reports WHERE id = ?");
$stmt->execute([$id]);
$incident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incident) {
    echo json_encode(['error' => 'Incident not found']);
    exit;
}

$reporter_contact = $incident['reporter_contact'];

// Update incident
$success = $incidentModel->updateIncident($id, $status, $responded_by);

if ($success && strtolower($status) === 'ongoing') {
    // ✅ Send SMS notification only when status = "ongoing"

    $username = "jarejare5kcux2025";  
    $password = "eTeDRLyd"; 
    $sender = "DisasterApp";         
    $recipient = $reporter_contact;  // From DB
    $message = "Hello from DisasterReadyApp! Incident status has been updated to '{$status}' by responder '{$responded_by}'.";

    $type = 0; // 0 = plain text

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.easysendsms.app/bulksms',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'username' => $username,
            'password' => $password,
            'from'     => $sender,
            'to'       => $recipient,
            'text'     => $message,
            'type'     => $type
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
}

echo json_encode([
    'success' => $success,
    'message' => "Incident updated successfully.",
    'sms_sent' => isset($response) ? true : false,
    'sms_response' => $response ?? null,
    'http_status' => $httpcode ?? null
]);
