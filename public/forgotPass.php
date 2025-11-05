<?php
require_once "../models/resetAccount.php";
require __DIR__ . '/../vendor/autoload.php';
require_once "../utils/rateLimiter.php";
require_once "../utils/sendEmail.php";
require_once "../config/db.php";

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$database = new Database();
$conn = $database->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data["email"]);

    if (empty($email)) {
        echo json_encode([
            "success" => false,
            "message" => "Email field is required."
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email format."
        ]);
        exit;
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);
    $archivedUser = $userModel->isArchived($email);

    if ($archivedUser) {
        echo json_encode([
            "success" => false,
            "message" => "You cannot reset the password of a deactivated account. Please contact the system administrator for assistance."
        ]);
        exit; }

    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "Email is not existing in the system."
        ]);
        exit;
    }

    $rateLimiter = new RateLimiter($conn);
    $rateLimiter->checkLimit($email, 'forgot_password');

    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

    $userModel->deleteResetTokenByEmail($email);
    $userModel->createResetToken($email, $token, $expires);

    $resetLink = "http://localhost:3000/reset-password?token=$token";
    $body = "
        Hi {$user['username']},<br><br>
        Click this link to reset your password:<br>
        <a href='$resetLink'>$resetLink</a><br><br>
        This link will expire in 5 minutes.
    ";

    $result = sendEmail(
        $email,
        "Disaster Ready Password Reset",
        $body
    );

    echo json_encode($result);
}
