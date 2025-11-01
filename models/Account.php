<?php
class Account {
    private $pdo;
    private $table = "users";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isEmailTaken($email, $excludeId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return $stmt->fetchColumn() > 0;
    }

    public function updateProfile($id, $username, $email) {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET username = ?, email = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$username, $email, $id]);
    }

    public function getPasswordById($id) {
        $stmt = $this->pdo->prepare("SELECT password FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    }

    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table} 
            SET password = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function addPasswordToHistory($id, $hashedPassword) {
        $stmt = $this->pdo->prepare("
            INSERT INTO password_history (user_id, password, created_at) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$id, $hashedPassword]);
    }

    public function getPasswordHistory($id) {
        $stmt = $this->pdo->prepare("
            SELECT password, created_at 
            FROM password_history 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
