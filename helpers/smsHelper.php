<?php
require_once "../config/sms.php";

function philsmsRequest($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . PHILSMS_API_TOKEN,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) return ['status' => 'error', 'message' => $err];

    $decoded = json_decode($response, true);
    return $decoded ?? ['status' => 'error', 'message' => 'Invalid JSON response', 'raw' => $response];
}
