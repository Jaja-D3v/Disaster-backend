<?php

require_once "../config/db.php";


$db = new Database();
$pdo = $db->connect();

$input = json_decode(file_get_contents("php://input"), true);
$eventType = $input["type"] ?? "";

if ($eventType === "payment_intent.paid") {
    $paymentIntentId = $input["data"]["id"];
    $paymentMethod = $input["data"]["attributes"]["payments"][0]["payment_method_type"] ?? null;

    $stmt = $pdo->prepare("UPDATE donations SET status='paid', payment_method=:pm WHERE payment_intent_id=:pi");
    $stmt->execute([
        ":pm" => $paymentMethod,
        ":pi" => $paymentIntentId
    ]);
}

// Respond 200 OK
http_response_code(200);
echo json_encode(["status" => "success"]);
