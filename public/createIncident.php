<?php


header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// // Just echo the raw received data
// $response = [
//     'method' => $_SERVER['REQUEST_METHOD'],
//     'POST'   => $_POST,
//     'FILES'  => $_FILES,
// ];

// // Return JSON for easy viewing in browser
// header("Content-Type: application/json");
// echo json_encode($response, JSON_PRETTY_PRINT);
// exit;

require_once "../controllers/createIncident.php";