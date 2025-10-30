<?php
require_once "../vendor/autoload.php";
require_once "../config/db.php";
require_once "../models/Donation.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$ENCRYPTION_KEY = $_ENV['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY');
if (!$ENCRYPTION_KEY) {
    http_response_code(500);
    echo json_encode(["error" => "Encryption key not set in .env"]);
    exit;
}

$db = new Database();
$pdo = $db->connect();

$donationModel = new Donation($pdo, $ENCRYPTION_KEY);

$donations = $donationModel->getAllDonations();

header("Content-Type: application/json");
echo json_encode($donations);
