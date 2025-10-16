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

    
}
