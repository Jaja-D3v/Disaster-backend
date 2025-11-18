<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php'; 
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../utils/telcoChecker.php';
require_once __DIR__ . '/../utils/smsSender.php';
require_once __DIR__ . '/../models/incident.php';
require_once __DIR__ . '/../models/BarangayContact.php';

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
$responseCaptcha = json_decode($verify);
if (!$responseCaptcha->success) {
    echo json_encode(['error' => 'Captcha verification failed']);
    exit;
}

$db = new Database();
$pdo = $db->connect();
$incidentModel = new Incident($pdo);


$reporter_name    = $_POST['reporter_name'] ?? null;
$reporter_contact = $_POST['reporter_contact'] ?? null;
$description      = $_POST['description'] ?? null;
$lat              = $_POST['lat'] ?? null;
$lng              = $_POST['lng'] ?? null;
$severity         = $_POST['severity'] ?? null;

$contactLength = strlen($reporter_contact);

if(substr($reporter_contact, 0, 3) !== '639' || $contactLength != 12) {
    echo json_encode(['error' => 'Invalid contact number format.']);
    exit;
}


if ($lat == 0.00000000) {
    echo json_encode(['error' => 'Please check your location settings and allow location access.']);
    exit;
}

if (!$reporter_contact || !$description) {
    echo json_encode(['error' => 'Contact and description are required']);
    exit;
}

// FILE UPLOAD HANDLING
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

// GEOLOCATION PROCESSING

$barangayName = 'Unknown Barangay';
$city = 'Unknown City';

$GEOAPIFY_KEY = $_ENV['GEOAPIFY_KEY'] ?? null;
if (!$GEOAPIFY_KEY) {
    echo json_encode(['error' => 'Geoapify API key not set in .env']);
    exit;
}

if ($lat && $lng) {
    $url = "https://api.geoapify.com/v1/geocode/reverse?lat=$lat&lon=$lng&format=json&apiKey=$GEOAPIFY_KEY";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "DisasterReadyApp/1.0 (contact: jaredabrera@example.com)");
    $responseGeo = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo json_encode([
            'error' => 'Geoapify reverse geocoding failed',
            'http_code' => $httpcode,
            'response' => $responseGeo
        ]);
        exit;
    }

   $data = json_decode($responseGeo, true);
    if (!empty($data['results'][0])) {
        $address = $data['results'][0] ;

        $barangayName = $address['suburb'] ?? 'Unknown Barangay';

        $city = $address['city'] ?? 'Unknown City';
        }
    }


if ($city !== "Los Baños") { 
    echo json_encode([
        'success' => false,
        'error' => 'Incidents can only be reported within Los Baños.'
    ]);
    exit;
}

// END OF GEOLOCATION PROCESSING

// SEND SMS TO BARANGAY CONTACT

$barangayModel = new BarangayContact($pdo);
$barangayInfo = $barangayModel->getByNumber($barangayName);
$recipient = $barangayInfo['contact_number'] ?? null;
$telcoChecker = new detectTelco($pdo);
$telco = $telcoChecker->detect($recipient);
$sms = new SmsService($pdo);

if($recipient && $city === "Los Baños"){

    if($telco !== 'smart' && $telco !== 'unknown'){ 

        $message = "Hello from DisasterReadyApp! You have a new incident report in your barangay.";
        $sms->sendGlobe($message, $recipient);
      
    }elseif ($telco === 'smart' && $recipient) {
        $message = "Hello from DisasterReadyApp! You have a new incident report in your barangay.";
        $sms->sendSmart($recipient, $message);

}   } 
// END OF SMS SENDING

    
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
    'city'      => $city,
    'sms_sent' => $recipient ? true : false,
    'telco' => $telco,
    "sms_result" => $result ?? null 
    
]);