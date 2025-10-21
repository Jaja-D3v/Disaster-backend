<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // ✅ Load environment variables
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // ✅ Server settings
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_EMAIL'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port = $_ENV['SMTP_PORT'];

        // ✅ Recipients
        $mail->setFrom($_ENV['SMTP_EMAIL'], $_ENV['FROM_NAME']);
        $mail->addAddress($to);

        // ✅ Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // ✅ Send email
        $mail->send();
        return [
            "success" => true,
            "message" => "Email sent successfully."
        ];

    } catch (Exception $e) {
        return [
            "success" => false,
            "message" => "Email could not be sent. Error: {$mail->ErrorInfo}"
        ];
    }
}
