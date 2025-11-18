<?php
require_once "../vendor/autoload.php";
require_once "../config/db.php";
require_once "../models/Donation.php";

use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$PAYMONGO_SECRET = $_ENV['PAYMONGO_SECRET_KEY'] ?? null;
$ENCRYPTION_KEY  = $_ENV['ENCRYPTION_KEY'] ?? null;
$RECAPTCHA_SECRET = $_ENV['RECAPTCHA_SECRET'] ?? null; 

if (!$PAYMONGO_SECRET || !$ENCRYPTION_KEY || !$RECAPTCHA_SECRET) {
    http_response_code(500);
    echo json_encode(["error" => "Environment variables not loaded properly."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if($input["type"] === "card") {
      

    if (strlen($input['details']['card_number']) !== 16) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid card number length"]);
        exit;
    }

    if (strlen($input['details']['cvc']) !== 3) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid CVC length"]);
        exit;
    }



    if ($input['details']['exp_month'] > 12 || $input['details']['exp_month'] < 1) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid expiration month"]);
        exit;
    }

    if (!is_numeric($input['details']['exp_year'])){
        http_response_code(400);
        echo json_encode(["error" => "Invalid year format"]);
        exit;
    }


    $currentYear = (int)date('y'); 
    $currentMonth = (int)date('m');

    if (
        (int)$input['details']['exp_year'] < $currentYear ||
        (
            (int)$input['details']['exp_year'] === $currentYear &&
            (int)$input['details']['exp_month'] < $currentMonth
        )
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Your card is already expired"]);
        exit;
    }


    
}

// ✅ reCAPTCHA verification

$captchaToken = $input["g-recaptcha-response"] ?? null;

if (!$captchaToken) {
    http_response_code(400);
    echo json_encode(["error" => "Missing reCAPTCHA token"]);
    exit;
}

$verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
$verifyResponse = file_get_contents($verifyUrl . "?secret=" . urlencode($RECAPTCHA_SECRET) . "&response=" . urlencode($captchaToken));
$captchaResult = json_decode($verifyResponse, true);

if (!$captchaResult["success"]) {
    http_response_code(400);
    echo json_encode(["error" => "Captcha verification failed"]);
    exit;
}
// ✅ end of reCAPTCHA verification

$requiredFields = ["amount", "description", "type", "name", "email"];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit;
    }
}

$paymentType = strtolower($input["type"]);

$intentData = [
    "data" => [
        "type" => "payment_intent",
        "attributes" => [
            "amount" => intval($input["amount"]),
            "currency" => "PHP",
            "description" => $input["description"],
            "statement_descriptor" => "DisasterReadyApp",
            "payment_method_allowed" => ["gcash", "grab_pay", "card", "paymaya"]
        ]
    ]
];

$ch = curl_init("https://api.paymongo.com/v1/payment_intents");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($intentData),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]
]);
$intentResponse = curl_exec($ch);
$intentResult   = json_decode($intentResponse, true);
curl_close($ch);

if (!isset($intentResult["data"]["id"])) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to create payment intent", "details" => $intentResult]);
    exit;
}

$paymentIntentId = $intentResult["data"]["id"];

$attributes = [
    "type" => $paymentType,
    "billing" => [
        "name"  => $input["name"],
        "email" => $input["email"]
    ]
];

if ($paymentType === "card" && isset($input["details"])) {
    $attributes["details"] = $input["details"];
}

$pmData = ["data" => ["attributes" => $attributes]];

$ch = curl_init("https://api.paymongo.com/v1/payment_methods");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($pmData),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]
]);
$pmResponse = curl_exec($ch);
$pmResult   = json_decode($pmResponse, true);
curl_close($ch);

if (!isset($pmResult["data"]["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Failed to create payment method", "details" => $pmResult]);
    exit;
}

$paymentMethodId = $pmResult["data"]["id"];

$attachData = [
    "data" => [
        "attributes" => [
            "payment_method" => $paymentMethodId,
            "return_url"     => $input["return_url"] ?? "https://yourfrontend.com/success"
        ]
    ]
];

$ch = curl_init("https://api.paymongo.com/v1/payment_intents/$paymentIntentId/attach");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($attachData),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]
]);
$attachResponse = curl_exec($ch);
$attachResult   = json_decode($attachResponse, true);
curl_close($ch);

$db = new Database();
$pdo = $db->connect();
$donationModel = new Donation($pdo, $ENCRYPTION_KEY);

$donation_id = $donationModel->create(
    $paymentIntentId,
    $input["name"],
    $input["email"],  
    intval($input["amount"]),
    "PHP",
    $paymentType,
    "pending"
);

echo json_encode([
    "success"        => true,
    "donation_id"    => $donation_id,
    "payment_intent" => $intentResult,
    "attach_result"  => $attachResult
]);
