<?php
header('Content-Type: application/json');

// ---------------------------
// HARD-CODED TEST CONFIG
// ---------------------------

// Hardcoded system status: 1 = ON, 0 = OFF
$system_status = 1;

// Hardcoded registered users
$users = [
    "+639197017084" => "Jared Abrera",
    "+639987654321" => "Alice Santos"
];

// ---------------------------
// SIMULATED INCOMING SMS
// ---------------------------
$input = json_decode(file_get_contents("php://input"), true);
$from    = $input['from'] ?? null;
$message = strtoupper(trim($input['message'] ?? ""));

// 1️⃣ Check system ON/OFF
if ($system_status == 0) {
    echo json_encode([
        "status" => "failed",
        "reason" => "System is OFF"
    ]);
    exit;
}

// 2️⃣ Check if sender is registered
if (!isset($users[$from])) {
    echo json_encode([
        "status" => "failed",
        "reason" => "Sender not registered"
    ]);
    exit;
}

// 3️⃣ Log report (simulated)
$report_log = [
    "user" => $users[$from],
    "from" => $from,
    "message" => $message,
    "timestamp" => date("Y-m-d H:i:s")
];

// ---------------------------
// 4️⃣ SIMULATED SMS REPLY
// ---------------------------
$reply_sms = "Report received. Thank you!";

// ---------------------------
// 5️⃣ PLACEHOLDER FOR REAL API
// ---------------------------
// To use EngageSPARK API later, uncomment below and fill your credentials:


$apiKey   = "ffdd28d4348b945d5fcc3f8a5593d6cd9978d969";
$orgId    = "17812";
$senderId = "wish";

$sendData = [
    "orgId" => $orgId,
    "messages" => [
        [
            "to" => $from,
            "from" => $senderId,
            "message" => $reply_sms
        ]
    ]
];

$options = [
    "http" => [
        "header"  => "Content-Type: application/json\r\nAuthorization: Bearer $apiKey\r\n",
        "method"  => "POST",
        "content" => json_encode($sendData),
    ]
];

$context  = stream_context_create($options);
$result   = file_get_contents("https://api.engagespark.com/v1/sms/phonenumbers", false, $context);
$api_response = json_decode($result, true);


$api_response = "SIMULATED_API_RESPONSE"; // placeholder while testing

// ---------------------------
// 6️⃣ RETURN RESPONSE
// ---------------------------
echo json_encode([
    "status" => "success",
    "report" => $report_log,
    "reply_sms" => $reply_sms,
    "api_response" => $api_response
]);
