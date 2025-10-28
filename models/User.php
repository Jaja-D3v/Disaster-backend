<?php
class User {
    private $pdo;
    private $table = "users";
    private $pendingTable = "pending_registrations";

    public function __construct($db) {
        $this->pdo = $db;
    }

    public function findByUsername($username) {
        $query = "SELECT id, username, email, password, role, barangay FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


        public function createUser($username, $email, $password, $role, $barangay) {
        $sql = "INSERT INTO users (username, email, password, role, barangay) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$username, $email, $password, $role, $barangay]);
    }

        public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

        
        public function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET last_logged_in = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function updateUserStatus($userId, $status) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

     // Pending registration methods
    public function createPending($username, $email, $password, $barangay, $code, $expires) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->pendingTable} (username, email, password, barangay, code, expires)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE code = ?, expires = ?
        ");
        return $stmt->execute([$username, $email, $password, $barangay, $code, $expires, $code, $expires]);
    }

     public function getPending($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->pendingTable} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deletePending($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->pendingTable} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countUsers() {
    $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'] ?? 0;

    }

    public function incrementLoginAttempts($username) {
        $query = "UPDATE users SET login_attempts = login_attempts + 1, last_attempt_at = NOW() WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
    }

    public function resetLoginAttempts($username) {
        $query = "UPDATE users SET login_attempts = 0, last_attempt_at = NULL WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
    }

    public function getUserAttempts($username) {
        $query = "SELECT login_attempts, last_attempt_at FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    
}
