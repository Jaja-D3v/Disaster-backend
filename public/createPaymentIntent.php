<?php
require_once "../config/config.php";
require_once "../config/db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
      http_response_code(200);
    exit();

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input['amount']) || !isset($input['description'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields: amount or description"]);
    exit;
}

// Build payload
$data = [
    "data" => [
        "type" => "payment_intent",
        "attributes" => [
            "amount" => intval($input["amount"]),
            "currency" => "PHP",
            "description" => $input["description"],
            "statement_descriptor" => "DisasterReadyApp",
            "payment_method_allowed" => ["gcash","grab_pay","card","paymaya"]
        ]
    ]
];

// cURL request to PayMongo
$ch = curl_init("https://api.paymongo.com/v1/payment_intents");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode($PAYMONGO_SECRET . ":")
]);

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resp = json_decode($response, true);

// Save donation to DB as pending
if (isset($resp["data"]["id"])) {
    $db = new Database();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("INSERT INTO donations (payment_intent_id, donor_name, donor_email, amount, currency, status) 
                           VALUES (:pi, :name, :email, :amount, :currency, :status)");
    $stmt->execute([
        ":pi" => $resp["data"]["id"],
        ":name" => $input["name"] ?? "Anonymous Donor",
        ":email" => $input["email"] ?? "noemail@disasterready.app",
        ":amount" => intval($input["amount"]),
        ":currency" => "PHP",
        ":status" => "pending"
    ]);
}

http_response_code($http_status);
echo $response;
}