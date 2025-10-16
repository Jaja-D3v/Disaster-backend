<?php
class RateLimiter {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function checkLimit($email, $endpoint, $limit = 10, $hours = 1) {
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt = $this->conn->prepare("
            SELECT COUNT(*) FROM password_request_logs
            WHERE (ip_address = ? OR email = ?)
            AND created_at > (NOW() - INTERVAL ? HOUR)
        ");
        $stmt->execute([$ip, $email, $hours]);
        $count = $stmt->fetchColumn();

        if ($count >= $limit) {
            http_response_code(429);
            echo json_encode([
                "success" => false,
                "message" => "Too many reset requests. Please try again later."
            ]);
            exit;
        }

        $stmt = $this->conn->prepare("INSERT INTO password_request_logs (ip_address, email) VALUES (?, ?)");
        $stmt->execute([$ip, $email]);

        $this->conn->query("DELETE FROM password_request_logs WHERE created_at < (NOW() - INTERVAL 7 DAY)");
    }
}
