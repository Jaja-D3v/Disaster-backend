<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';
require_once __DIR__ . '/../utils/telcoChecker.php';
require_once __DIR__ . '/../utils/smsSender.php';


$db = new Database();
$pdo = $db->connect();
$incidentModel = new Incident($pdo);

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? $_POST['id'] ?? null;
$status = $data['status'] ?? $_POST['status'] ?? null;
$responded_by = $data['responded_by'] ?? $_POST['responded_by'] ?? null;

if (!$id || !$status) {
    echo json_encode(['error' => 'Incident ID and status are required']);
    exit;
}


$stmt = $pdo->prepare("SELECT reporter_contact FROM incident_reports WHERE id = ?");
$stmt->execute([$id]);
$incident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incident) {
    echo json_encode(['error' => 'Incident not found']);
    exit;
}

$reporter_contact = $incident['reporter_contact'];

$recipient = $reporter_contact;  // From DB
$success = $incidentModel->updateIncident($id, $status, $responded_by);
$telcoChecker = new detectTelco($pdo);
$telco = $telcoChecker->detect($recipient);

if ($success && strtolower($status) === 'ongoing') {
    

    if($telco === 'globe' || $telco === 'dito') {
        
        $message = "Hello from DisasterReadyApp! The responder is on the way. Responder: {$responded_by}.";
        $type = 0; 
        $sms = new SmsService($pdo);
        $message = "Hello from DisasterReadyApp! You have a new incident report in your barangay.";
        $result = $sms->sendGlobe($message, $recipient);
        if (!$result) {
            error_log("Failed to send SMS to $recipient");
        }

    }elseif ($telco === 'smart') {
        $sms = new SmsService($pdo);
        $message = "Hello from DisasterReadyApp! The responder is on the way. Responder: {$responded_by}.";
        $result = $sms->sendSmart($recipient, $message);

        if (!$result["success"]) {
            error_log("Failed to send SMS to $recipient");
        }
    }
    
}

echo json_encode([
    'success' => $success,
    'message' => "Incident updated successfully.",
    'sms_sent' => isset($response) ? true : false,
    'sms_response' => $response ?? null,
    'http_status' => $httpcode ?? null,
    'telco' => $telco ?? null
]);
