<?php
require_once "../models/resetAccount.php";
require_once "../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data["email"]);

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // para sa old tokens  
        $userModel->deleteResetTokenByEmail($email);
        // Create a new token
        $userModel->createResetToken($email, $token, $expires);

        // link na mapupunta sa email
        $resetLink = "http://localhost/Disaster-backend/controllers/resetPassword.php?token=$token";

        // para sa pag send ng email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "jaredabrera44@gmail.com";
            $mail->Password = "zmfz sqkz fnor rwcn"; // Gmail app password
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom("disasterready@gmail.com", "DisasterReady App");
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "DisasterReady Password Reset";
            $mail->Body = "
                Hi {$user['username']},<br><br>
                Click this link to reset your password:<br>
                <a href='$resetLink'>$resetLink</a><br><br>
                This link will expire in 15 minutes.
            ";

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
        "message" => "If this email exists, a reset link has been sent."
    ]);
}
