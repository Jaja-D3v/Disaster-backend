<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';
require_once __DIR__ . '/../models/BarangayContact.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// $recaptchaSecret = $_ENV['RECAPTCHA_SECRET'] ?? null;
// if (!$recaptchaSecret) {
//     echo json_encode(['error' => 'reCAPTCHA secret key not found']);
//     exit;
// }

// $captcha = $_POST['g-recaptcha-response'] ?? null;
// if (!$captcha) {
//     echo json_encode(['error' => 'Captcha missing']);
//     exit;
// }

// $verify = file_get_contents(
//     "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$captcha"
// );
// $responseCaptcha = json_decode($verify);
// if (!$responseCaptcha->success) {
//     echo json_encode(['error' => 'Captcha verification failed']);
//     exit;
// }

$db = new Database();
$pdo = $db->connect();
$incidentModel = new Incident($pdo);
$data = json_decode(file_get_contents('php://input'), true);

$reporter_name = $data['reporter_name'] ?? null;
$reporter_contact = $data['reporter_contact'] ?? null;
$description = $data['description'] ?? null;
$lat = $data['lat'] ?? null;
$lng = $data['lng'] ?? null;
$severity = $data['severity'] ?? null;

if (!$reporter_contact || !$description) {
    echo json_encode(['error' => 'Contact and description are required']);
    exit;
}

$media_path = null;
if (!empty($_FILES['media']['name'])) {
    $uploadDir = __DIR__ . '/../uploads/incidentPhotos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['media']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
        $media_path = 'incidentPhotos/' . $fileName;
    } else {
        echo json_encode(['error' => 'Failed to upload file']);
        exit;
    }
}


$barangayName = 'Unknown Barangay';
if ($lat && $lng) {
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lng&zoom=18&addressdetails=1";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "DisasterReady/1.0 (contact: jaredabrera@example.com)");
    $responseGeo = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200 && $responseGeo) {
        $data = json_decode($responseGeo, true);
        $barangayName = $data['address']['quarter']
            ?? $data['address']['suburb']
            ?? $data['address']['village']
            ?? $data['address']['neighbourhood']
            ?? 'Unknown Barangay';

         $city = $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['municipality']
            ?? $data['address']['county']
            ?? 'Unknown City';

    
    }
}

if ($city !== "Los Baños") { 
    echo json_encode([
        'success' => false,
        'error' => 'Incidents can only be reported within Los Baños.'
    ]);
    exit;
}


$barangayModel = new BarangayContact($pdo);
$barangayInfo = $barangayModel->getByNumber($barangayName);
$recipient = $barangayInfo['contact_number'] ?? null;

if ($recipient && $city === "Los Baños") {
    $username = "jarejare5kcux2025";  
    $password = "eTeDRLyd"; 
    $sender = "wish";         
    $message = "Hello from DisasterReadyApp! You have a new incident report in your barangay.";

    $type = 0;

    $chSMS = curl_init();
    curl_setopt_array($chSMS, [
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

    $smsResponse = curl_exec($chSMS);
    $smsCode = curl_getinfo($chSMS, CURLINFO_HTTP_CODE);
    curl_close($chSMS);

    if ($smsCode !== 200) {
        error_log("SMS failed for $recipient: $smsResponse");
    }



} 

$incidentModel->createIncident(
    $reporter_name,
    $reporter_contact,
    $description,
    $lat,
    $lng,
    $severity,
    $media_path
);

echo json_encode([
    'success' => true,
    'message' => 'Incident reported successfully',
    'media' => $media_path,
    'barangay' => $barangayName,
    'sms_sent' => $recipient ? true : false
]);