<?php
class Account {
    private $pdo;
    private $table = "users";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    
    public function updateProfile($id, $username, $email, $password) {
        
        $stmt = $this->pdo->prepare("SELECT password, username, email FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ["success" => false, "message" => "User not found."];
        }

        
        if (!password_verify($password, $user['password'])) {
            return ["success" => false, "message" => "Password is incorrect."];
        }

       
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->rowCount() > 0) {
            return ["success" => false, "message" => "Email is already in use."];
        }

       
        if ($username === $user['username'] && $email === $user['email']) {
            return ["success" => false, "message" => "No changes made."];
        }

       
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET username = ?, email = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$username, $email, $id]);

        if ($stmt->rowCount() > 0) {
            return ["success" => true, "message" => "Profile updated successfully."];
        } else {
            return ["success" => false, "message" => "Failed to update profile."];
        }
    }

    
    public function changePassword($id, $currentPassword, $newPassword) {
  
    $stmt = $this->pdo->prepare("SELECT password FROM {$this->table} WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return ["success" => false, "message" => "User not found."];
    }

    
    if (!password_verify($currentPassword, $user['password'])) {
        return ["success" => false, "message" => "Current password is incorrect."];
    }

    
    if (password_verify($newPassword, $user['password'])) {
        return ["success" => false, "message" => "No changes made. The new password cannot be the same as the current one."];
    }

    
    $stmt = $this->pdo->prepare("
        SELECT password, created_at 
        FROM password_history 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($history as $old) {
        if (password_verify($newPassword, $old['password'])) {
            $usedDate = new DateTime($old['created_at']);
            $now = new DateTime();
            $interval = $usedDate->diff($now);

            if ($interval->m + ($interval->y * 12) < 5) {
                return ["success" => false, "message" => "This password was already used within the last 5 months. Please choose a different one."];
            }
        }
    }

  
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $this->pdo->prepare("UPDATE {$this->table} SET password = ?, updated_at = NOW() WHERE id = ?");
    if ($stmt->execute([$hashedPassword, $id])) {
        // Step 6: Save old password in history
        $stmt = $this->pdo->prepare("INSERT INTO password_history (user_id, password) VALUES (?, ?)");
        $stmt->execute([$id, $hashedPassword]);

        return ["success" => true, "message" => "Password changed successfully."];
    }

    return ["success" => false, "message" => "Failed to change password."];
}

}
