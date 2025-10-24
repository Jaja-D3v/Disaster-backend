<?php


require_once '../vendor/autoload.php';
require_once "../config/db.php";
require_once '../models/Donation.php';

use GuzzleHttp\Client;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$donationModel = new Donation($pdo);

// Get POST data
$name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$amount = floatval($_POST['amount']);
$payment_method = trim($_POST['payment_method']);

// Insert donation (calls Model)
$donation_id = $donationModel->create($name, $phone, $amount, $payment_method);

// Create PayMongo Payment Intent (business logic)
$client = new Client();
$secret_key = $_ENV['PAYMONGO_SECRET_KEY'];

$response = $client->request('POST', 'https://api.paymongo.com/v1/payment_intents', [
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($secret_key . ':')
    ],
    'json' => [
        'amount' => $amount * 100,
        'currency' => 'PHP',
        'payment_method_allowed' => [$payment_method],
        'description' => "Donation ID: $donation_id"
    ]
]);

$body = json_decode($response->getBody(), true);
$payment_url = $body['data']['attributes']['next_action']['redirect']['url'];

// Redirect donor
header("Location: $payment_url");
exit;
