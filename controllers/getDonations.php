<?php
require_once "../vendor/autoload.php";
require_once "../config/db.php";
require_once "../models/Donation.php";

use Dotenv\Dotenv;

// ✅ Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// ✅ Get encryption key
$ENCRYPTION_KEY = $_ENV['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY');
if (!$ENCRYPTION_KEY) {
    http_response_code(500);
    echo json_encode(["error" => "Encryption key not set in .env"]);
    exit;
}

// ✅ Connect to database
$db = new Database();
$pdo = $db->connect();

// ✅ Initialize Donation model
$donationModel = new Donation($pdo, $ENCRYPTION_KEY);

// ✅ Fetch all donations (decrypted)
$donations = $donationModel->getAllDonations();

// ✅ Return JSON response
header("Content-Type: application/json");
echo json_encode($donations);
