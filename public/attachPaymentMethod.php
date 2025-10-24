<?php
require_once "../config/config.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["payment_intent_id"]) || !isset($input["type"])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing payment_intent_id or type"]);
    exit;
}

$paymentType = strtolower($input["type"]);
$attributes = ["type" => $paymentType];

if (isset($input["name"]) || isset($input["email"])) {
    $attributes["billing"] = [
        "name" => $input["name"] ?? "Anonymous Donor",
        "email" => $input["email"] ?? "noemail@disasterready.app"
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

if (!isset($pm_result["data"]["id"])) {
    http_response_code(400);
    echo json_encode(["error" => "Failed to create payment method", "details" => $pm_result]);
    exit;
}

$paymentMethodId = $pm_result["data"]["id"];
$paymentIntentId = $input["payment_intent_id"];

// Attach Payment Method
$attach_data = [
    "data" => [
        "attributes" => [
            "payment_method" => $paymentMethodId,
            "return_url" => $input["return_url"] ?? "https://yourfrontend.com/success"
        ]
    ]
];

$ch = curl_init("https://api.paymongo.com/v1/payment_intents/$paymentIntentId/attach");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($attach_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
]);
$attach_response = curl_exec($ch);
$attach_result = json_decode($attach_response, true);
curl_close($ch);

echo $attach_response;
