<?php
require_once "../models/resetAccount.php";
require __DIR__ . '/../vendor/autoload.php';
require_once "../utils/rateLimiter.php";
require_once "../utils/sendEmail.php";
require_once "../config/db.php";

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

// Load .env variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Database connection
$database = new Database();
$conn = $database->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data["email"]);

    // Check if email is provided
    if (empty($email)) {
        echo json_encode([
            "success" => false,
            "message" => "Email field is required."
        ]);
        exit;
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    // ✅ If email not found, return error
    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "Email is not existing in the system."
        ]);
        exit;
    }

    // ✅ Rate limiter check
    $rateLimiter = new RateLimiter($conn);
    $rateLimiter->checkLimit($email, 'forgot_password');

    // Generate token
    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    // Delete old tokens
    $userModel->deleteResetTokenByEmail($email);

    // Create new reset token
    $userModel->createResetToken($email, $token, $expires);

    $resetLink = "http://localhost/Disaster-backend/controllers/resetPassword.php?token=$token";
    $body = "
        Hi {$user['username']},<br><br>
        Click this link to reset your password:<br>
        <a href='$resetLink'>$resetLink</a><br><br>
        This link will expire in 5 minutes.
    ";

    // Send email using reusable function
    $result = sendEmail(
        $email,
        "Disaster Ready Password Reset",
        $body
    );

    echo json_encode($result);
}
