if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data["email"]);
    $type = $data["type"] ?? "link"; // 🔹 "link" or "code"

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    $rateLimiter = new RateLimiter($conn);
    $rateLimiter->checkLimit($email, 'forgot_password');

    if ($user) {
        if ($type === "code") {
            // 🔹 Generate 6-digit verification code
            $code = rand(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            // Store the code in DB (you can reuse your reset_tokens table)
            $userModel->deleteResetTokenByEmail($email);
            $userModel->createResetToken($email, $code, $expires);

            $body = "
                Hi {$user['username']},<br><br>
                Your verification code is: <b>$code</b><br><br>
                This code will expire in 5 minutes.
            ";
        } else {
            // 🔹 Default: Token-based reset link
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            $userModel->deleteResetTokenByEmail($email);
            $userModel->createResetToken($email, $token, $expires);

            $resetLink = "http://localhost/Disaster-backend/controllers/resetPassword.php?token=$token";
            $body = "
                Hi {$user['username']},<br><br>
                Click this link to reset your password:<br>
                <a href='$resetLink'>$resetLink</a><br><br>
                This link will expire in 5 minutes.
            ";
        }

        // ✅ Send email using PHPMailer (reused)
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
            $mail->Subject = "Disaster Ready Password Reset";
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
        "message" => "If this email exists, a reset message has been sent."
    ]);
}
