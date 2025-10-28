<?php
require_once "../config/config.php";
require_once "../config/db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    // Basic validation
    if (!isset($input["amount"]) || !isset($input["description"]) || !isset($input["type"])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields: amount, description, or type"]);
        exit;
    }

    $paymentType = strtolower($input["type"]);

    // STEP 1: Create Payment Intent
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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($intentData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]);
    $intentResponse = curl_exec($ch);
    $intentStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $intentResult = json_decode($intentResponse, true);

    if (!isset($intentResult["data"]["id"])) {
        http_response_code($intentStatus);
        echo json_encode(["error" => "Failed to create payment intent", "details" => $intentResult]);
        exit;
    }

    $paymentIntentId = $intentResult["data"]["id"];

    // STEP 2: Create Payment Method
    $attributes = ["type" => $paymentType];

    if (isset($input["name"]) || isset($input["email"])) {
        $attributes["billing"] = [
            "name" => $input["name"] ?? "Anonymous Donor",
            "email" => $input["email"] ?? "noemail@disasterready.app"
        ];
    }

    if ($paymentType === "card" && isset($input["details"])) {
        $attributes["details"] = $input["details"];
    }

    $pmData = ["data" => ["attributes" => $attributes]];

    $ch = curl_init("https://api.paymongo.com/v1/payment_methods");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($pmData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]);
    $pmResponse = curl_exec($ch);
    $pmResult = json_decode($pmResponse, true);
    curl_close($ch);

    if (!isset($pmResult["data"]["id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Failed to create payment method", "details" => $pmResult]);
        exit;
    }

    $paymentMethodId = $pmResult["data"]["id"];

    // STEP 3: Attach payment method
    $attachData = [
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($attachData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
    ]);
    $attachResponse = curl_exec($ch);
    curl_close($ch);

    $attachResult = json_decode($attachResponse, true);

    // Save donation to DB
    $db = new Database();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("INSERT INTO donations (payment_intent_id, donor_name, donor_email, amount, currency, status)
                           VALUES (:pi, :name, :email, :amount, :currency, :status)");
    $stmt->execute([
        ":pi" => $paymentIntentId,
        ":name" => $input["name"] ?? "Anonymous Donor",
        ":email" => $input["email"] ?? "noemail@disasterready.app",
        ":amount" => intval($input["amount"]),
        ":currency" => "PHP",
        ":status" => "pending"
    ]);

    echo json_encode([
        "success" => true,
        "payment_intent" => $intentResult,
        "attach_result" => $attachResult
    ]);
}
