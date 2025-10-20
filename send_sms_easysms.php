<?php
// ----------------------------
// EasySendSMS Test Script
// ----------------------------


$username = "jarejare5kcux2025";  
$password = "eTeDRLyd"; 

// SMS details
$responder = "John Doe";
$recipient = "639922620912";      
$sender = "DisasterApp";         
$message = "Hello from DisasterReadyApp! Incident status has been updated to '{$status}' by responder '{$responded_by}'.";

$type = 0;                        // 0 = plain text, 1 = Unicode

// Initialize cURL
$curl = curl_init();

// Setup cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.easysendsms.app/bulksms',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'username' => $username,
        'password' => $password,
        'from'     => $sender,
        'to'       => $recipient,
        'text'     => $message,
        'type'     => $type
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_TIMEOUT => 30,
]);

// Execute cURL
$response = curl_exec($curl);

// Debug: Check for cURL errors
if(curl_errno($curl)){
    echo "cURL Error: " . curl_error($curl);
} else {
    echo "Response from EasySendSMS: " . $response;
}

// Debug: HTTP status code
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
echo "\nHTTP Status Code: " . $httpcode;

// Close cURL
curl_close($curl);
?>
