<?php
class Account {
    private $pdo;
    private $table = "users";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Update username & email with password confirmation
    public function updateProfile($id, $username, $email, $password) {
        // 1. Get current password hash from DB
        $stmt = $this->pdo->prepare("SELECT password FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ["success" => false, "message" => "User not found."];
        }

        // 2. Verify current password
        if (!password_verify($password, $user['password'])) {
            return ["success" => false, "message" => "Password is incorrect."];
        }

        // 3. Check if email already exists for another user
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->rowCount() > 0) {
            return ["success" => false, "message" => "Email is already in use."];
        }

        // 4. Update username & email
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET username = ?, email = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$username, $email, $id])) {
            return ["success" => true, "message" => "Profile updated successfully."];
        }

        return ["success" => false, "message" => "Failed to update profile."];
    }

    // Change password
    public function changePassword($id, $currentPassword, $newPassword) {
        // Get current password hash
        $stmt = $this->pdo->prepare("SELECT password FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ["success" => false, "message" => "User not found."];
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ["success" => false, "message" => "Current password is incorrect."];
        }

        // Hash and update new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET password = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $id])) {
            return ["success" => true, "message" => "Password changed successfully."];
        }

        return ["success" => false, "message" => "Failed to change password."];
    }
}
