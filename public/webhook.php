<?php
require_once "../config/db.php";

$db = new Database();
$pdo = $db->connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$payload = file_get_contents('php://input');

file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " | Raw payload: " . $payload . PHP_EOL, FILE_APPEND);

$input = json_decode($payload, true);

$eventType = $input['data']['attributes']['type'] ?? '';
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " | Received event: $eventType" . PHP_EOL, FILE_APPEND);

if (in_array($eventType, ["payment.paid", "payment_intent.paid"])) {

    $paymentData = $input['data']['attributes']['data'] ?? null;
    if ($paymentData) {

        $paymentIntentId = $paymentData['attributes']['payment_intent_id'] 
                           ?? $paymentData['id'] 
                           ?? null;

        
        $paymentMethod = 'unknown';
        $payments = $paymentData['attributes']['payments'] ?? [];
        if (!empty($payments) && isset($payments[0]['payment_method_type'])) {
            $paymentMethod = $payments[0]['payment_method_type'];
        } elseif (isset($paymentData['attributes']['source']['type'])) {
            $paymentMethod = $paymentData['attributes']['source']['type'];
        }

        if ($paymentIntentId) {
            try {
                $stmt = $pdo->prepare("UPDATE donations SET status='paid', payment_method=:pm WHERE payment_intent_id=:pi");
                $stmt->execute([
                    ":pm" => $paymentMethod,
                    ":pi" => $paymentIntentId
                ]);

                file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " | Updated donation $paymentIntentId with $paymentMethod\n", FILE_APPEND);
            } catch (PDOException $e) {
                file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " | DB error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            }
        }
    }
}

http_response_code(200);
echo json_encode([
    "statusCode" => 200,
    "body" => ["message" => "SUCCESS"]
]);
