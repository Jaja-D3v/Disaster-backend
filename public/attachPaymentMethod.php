<?php
require_once "../config/config.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    
// Get input
$input = json_decode(file_get_contents("php://input"), true);


if (!isset($input["payment_intent_id"]) || !isset($input["type"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing payment_intent_id or type"]);
    exit;
}

$paymentIntentId = $input["payment_intent_id"];
$paymentType = strtolower($input["type"]);

// Prepare attributes for creating payment method
$attributes = ["type" => $paymentType];

// Add billing info if provided
if (isset($input["name"]) || isset($input["email"])) {
    $attributes["billing"] = [
        "name" => $input["name"] ?? "Anonymous Donor",
        "email" => $input["email"] ?? "noemail@disasterready.app"
    ];
}

// Add card details if type is "card"
if ($paymentType === "card") {
    if (!isset($input["details"])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing card details"]);
        exit;
    }
    $attributes["details"] = [
        "card_number" => $input["details"]["card_number"],
        "exp_month"   => $input["details"]["exp_month"],
        "exp_year"    => $input["details"]["exp_year"],
        "cvc"         => $input["details"]["cvc"]
    ];
}

// Create Payment Method
$pm_data = ["data" => ["attributes" => $attributes]];

$ch = curl_init("https://api.paymongo.com/v1/payment_methods");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pm_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
]);
$pm_response = curl_exec($ch);
$pm_result = json_decode($pm_response, true);
curl_close($ch);

// Check if Payment Method was created successfully
if (!isset($pm_result["data"]["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Failed to create payment method", "details" => $pm_result]);
    exit;
}

$paymentMethodId = $pm_result["data"]["id"];

// Prepare attach data
$attach_data = [
    "data" => [
        "attributes" => [
            "payment_method" => $paymentMethodId,
            "return_url" => $input["return_url"] ?? "https://yourfrontend.com/success"
        ]
    ]
];

// Attach Payment Method to Payment Intent
$ch = curl_init("https://api.paymongo.com/v1/payment_intents/$paymentIntentId/attach");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($attach_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
]);
$attach_response = curl_exec($ch);
curl_close($ch);

// Return PayMongo response
echo $attach_response;



   
}