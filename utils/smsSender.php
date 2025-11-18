<?php
require_once "../helpers/smsHelper.php";

class SmsService {
    private $username;
    private $password;
    private $sender;
    private $apiUrl;
    private $sender_id;
    private $type;

    public function __construct() {
        $this->username = "jarejare5kcux2025";  
        $this->password = "eTeDRLyd"; 
        $this->sender   = "wish";
        $this->apiUrl   = "https://api.easysendsms.app/bulksms";
        $this->sender_id = 'PhilSMS';
        $this->type = 'plain';
    }

    public function sendSmart($recipient, $message, $type = 0) {
        if (!$recipient) {
            return [
                "success" => false,
                "error"   => "No recipient provided."
            ];
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'username' => $this->username,
                'password' => $this->password,
                'from'     => $this->sender,
                'to'       => $recipient,
                'text'     => $message,
                'type'     => $type
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode === 200) {
            return [
                "success" => true,
                "response" => $response
            ];
        }

        // Log error
        error_log("SMS failed to $recipient: $response");

        return [
            "success" => false,
            "error"   => "SMS sending failed",
            "response" => $response
        ];
    }


     function sendGlobe($messageInput, $recipientInput) {
        $recipient = $recipientInput;
        $sender_id = $this->sender_id;
        $type = $this->type;
        $message = $messageInput;
        $dlt_template_id = $input['dlt_template_id'] ?? null;

        if (!$recipient || !$message) {
            echo json_encode(['status' => 'error', 'message' => 'recipient, sender_id, and message are required']);
            return;
        }

        $data = [
            'recipient' => $recipient,
            'sender_id' => $sender_id,
            'type' => $type,
            'message' => $message
        ];

        if ($dlt_template_id) $data['dlt_template_id'] = $dlt_template_id;

        $response = philsmsRequest('POST', PHILSMS_SMS_URL, $data);
        echo json_encode($response);
    }
}
