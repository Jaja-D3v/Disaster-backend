<?php
require_once "../config/db.php";

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
        $this->conn->exec("SET time_zone = '+08:00'");
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteResetTokenByToken($token) {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE token LIKE ?");
        return $stmt->execute([$token]);
    }

    public function createResetToken($email, $token, $expires) {
        $stmt = $this->conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$email, $token, $expires]);
    }

        
    public function updatePassword($email, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?");
        return $stmt->execute([$hashedPassword, $email]);
    }

    // pag sucess yung reset, delete yung token
    public function deleteResetTokenByEmail($email) {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE email = ?");
        return $stmt->execute([$email]);
    }
        

    
    public function validateToken($token) {
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE token LIKE ?");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            return false; 
        }

        $currentTime = date("Y-m-d H:i:s");
        if ($currentTime > $reset['expires_at']) {
            
            $this->deleteResetTokenByToken($token);
            return false; 
        }

        return $reset; 
    }

       
    public function deleteResetByToken($token) {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE token LIKE ?");
        return $stmt->execute([$token]);
    }

    public function resetPasswordWithToken($token, $newPassword, $confirmPassword) {
    // Validate token
    $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        return ["success" => false, "message" => "Invalid or expired token."];
    }

    // Check expiry
    $currentTime = date("Y-m-d H:i:s");
    if ($currentTime > $reset['expires_at']) {
        $this->deleteResetTokenByToken($token);
        return ["success" => false, "message" => "This token has expired."];
    }

    $email = $reset['email'];

    // Validate password match
    if ($newPassword !== $confirmPassword) {
        return ["success" => false, "message" => "Passwords do not match."];
    }

    
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $newPassword)) {
        return ["success" => false, "message" => "Password must be at least 8 characters long and include uppercase, lowercase, and a number."];
    }

    
    $stmt = $this->conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ["success" => false, "message" => "User not found."];
    }

    $userId = $user['id'];

    
    if (password_verify($newPassword, $user['password'])) {
        return ["success" => false, "message" => "New password cannot be the same as the current one."];
    }

    
    $stmt = $this->conn->prepare("
        SELECT password, created_at 
        FROM password_history 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($history as $old) {
        if (password_verify($newPassword, $old['password'])) {
            $usedDate = new DateTime($old['created_at']);
            $now = new DateTime();
            $interval = $usedDate->diff($now);

            // Check if within 6 months
            if ($interval->m + ($interval->y * 12) < 6) {
                return ["success" => false, "message" => "This password was already used within the last 6 months. Please choose a different one."];
            }
        }
    }

    
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    
    $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    if ($stmt->execute([$hashedPassword, $userId])) {
        
        $stmt = $this->conn->prepare("INSERT INTO password_history (user_id, password, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $hashedPassword]);

        
        $this->deleteResetTokenByToken($token);

        return ["success" => true, "message" => "Password has been reset successfully."];
    }

    return ["success" => false, "message" => "Failed to update password."];
}


    
}
