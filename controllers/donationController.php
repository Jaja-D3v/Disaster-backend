<?php
require_once "../vendor/autoload.php";
require_once "../config/db.php";
require_once "../models/Donation.php";

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$PAYMONGO_SECRET = $_ENV['PAYMONGO_SECRET_KEY'] ?? null;
$ENCRYPTION_KEY   = $_ENV['ENCRYPTION_KEY'] ?? null;

if (!$PAYMONGO_SECRET || !$ENCRYPTION_KEY) {
    http_response_code(500);
    echo json_encode(["error" => "Environment variables not loaded properly."]);
    exit;
}

// Headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);
$requiredFields = ["amount", "description", "type", "name", "email"];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit;
    }
}

$paymentType = strtolower($input["type"]);

// -------------------------
// Step 1: Create Payment Intent
// -------------------------
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

// -------------------------
// Step 2: Create Payment Method
// -------------------------
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

// -------------------------
// Step 3: Attach Payment Method
// -------------------------
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

// -------------------------
// Step 4: Save donation to DB
// -------------------------
$db = new Database();
$pdo = $db->connect();
$donationModel = new Donation($pdo, $ENCRYPTION_KEY);

$donation_id = $donationModel->create(
    $paymentIntentId,
    $input["name"],   // plain text
    $input["email"],  // plain text
    intval($input["amount"]),
    "PHP",
    $paymentType,
    "pending"
);

// -------------------------
// Step 5: Return response
// -------------------------
echo json_encode([
    "success"        => true,
    "donation_id"    => $donation_id,
    "payment_intent" => $intentResult,
    "attach_result"  => $attachResult
]);
