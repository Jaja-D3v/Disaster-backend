<?php
require_once "../models/resetAccount.php";
require __DIR__ . '/../vendor/autoload.php';
require_once "../utils/rateLimiter.php";
require_once "../config/db.php";

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$database = new Database();
$conn = $database->connect();

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data["email"]);
    $type = isset($data["type"]) ? $data["type"] : "forgot_password"; // default type

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    $rateLimiter = new RateLimiter($conn);
    $rateLimiter->checkLimit($email, "forgot_password"); // use type for rate limiting

    if ($user) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_EMAIL'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['SMTP_EMAIL'], $_ENV['FROM_NAME']);
            $mail->addAddress($email);
            $mail->isHTML(true);

            if ($type === "create_account") {
                // ✅ Account verification email
                $verificationCode = rand(100000, 999999);
                // Save code + expiry in DB if needed
                $body = "Hi {$user['username']},<br><br>
                         Your account verification code is: <b>$verificationCode</b><br>
                         It will expire in 5 minutes.";
                $mail->Subject = "Disaster Ready Account Verification";
            } else {
                // ✅ Forgot password email
                $token = bin2hex(random_bytes(16));
                $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                $userModel->deleteResetTokenByEmail($email);
                $userModel->createResetToken($email, $token, $expires);

                $resetLink = "http://localhost/Disaster-backend/controllers/resetPassword.php?token=$token";
                $body = "Hi {$user['username']},<br><br>
                         Click this link to reset your password:<br>
                         <a href='$resetLink'>$resetLink</a><br><br>
                         This link will expire in 5 minutes.";
                $mail->Subject = "Disaster Ready Password Reset";
            }

            $mail->Body = $body;
            $mail->send();

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Email could not be sent. Error: {$mail->ErrorInfo}"
            ]);
            exit;
        }
    }

    echo json_encode([
        "success" => true,
        "message" => ($type === "create_account") 
                     ? "Verification email sent." 
                     : "If this email exists, a reset link has been sent."
    ]);
}
