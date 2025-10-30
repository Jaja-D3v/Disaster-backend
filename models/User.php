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

    public function deactivateUser($userId, $superAdminId, $superAdminPassword) {
    // Verify Super Admin
    $superAdmin = $this->findById($superAdminId);
    if (!$superAdmin || $superAdmin['role'] != 1 || !password_verify($superAdminPassword, $superAdmin['password'])) {
        return ["success"=>false,"message"=>"Invalid Super Admin credentials"];
    }
$user = $this->findById($userId);
if (!$user) {
    return ["success" => false, "message" => "User does not exist"];
}
if ($user['role'] == 1) {
    return ["success" => false, "message" => "Cannot deactivate Super Admin"];
}


    
    // Remove archived_at if exists to allow DB default
    if (isset($user['archived_at'])) {
        unset($user['archived_at']);
    }

       // Set status to deactivated before moving to archive
    $user['status'] = 'deactivated';

    // Copy to archived_users
    $columns = implode(", ", array_keys($user));
    $placeholders = ":" . implode(", :", array_keys($user));
    $stmt = $this->pdo->prepare("INSERT INTO archived_users ($columns) VALUES ($placeholders)");
    $stmt->execute($user);

    // Delete from users
    $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    return ["success"=>true,"message"=>"User deactivated and moved to archive"];
}



public function activateUser($userId, $superAdminId, $superAdminPassword) {
    // Verify Super Admin
    $superAdmin = $this->findById($superAdminId);
    if (!$superAdmin || $superAdmin['role'] != 1 || !password_verify($superAdminPassword, $superAdmin['password'])) {
        return ["success" => false, "message" => "Invalid Super Admin credentials"];
    }

    // Fetch user from archived_users
    $user = $this->findArchivedById($userId);
    if (!$user) return ["success" => false, "message" => "Archived user not found"];

    // Remove archived_at before inserting back
    if (isset($user['archived_at'])) {
        unset($user['archived_at']);
    }

    // Set status to active
    $user['status'] = 'active';

    // Begin transaction
    $this->pdo->beginTransaction();
    try {
        // Prepare column names and placeholders
        $columns = implode(", ", array_keys($user));
        $placeholders = ":" . implode(", :", array_keys($user));

        // Insert into users table
        $stmt = $this->pdo->prepare("INSERT INTO users ($columns) VALUES ($placeholders)");
        $stmt->execute($user);

        // Delete from archive
        $stmt = $this->pdo->prepare("DELETE FROM archived_users WHERE id = ?");
        $stmt->execute([$userId]);

        $this->pdo->commit();
        return ["success" => true, "message" => "User activated successfully"];
    } catch (PDOException $e) {
        $this->pdo->rollBack();
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}



public function findById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function findArchivedById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM archived_users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    // Fetch all users with optional filtering
    public function getAllUsers() {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, role, status, barangay, last_logged_in, created_at, updated_at
            FROM {$this->table}
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getAllArchivedUsers() {
    $sql = "SELECT id, username, email, role, status, barangay, archived_at 
            FROM archived_users 
            ORDER BY archived_at DESC";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $archivedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($archivedUsers)) {
        return ["success" => false, "message" => "No archived users found"];
    }

    return ["success" => true, "data" => $archivedUsers];
}

public function findArchivedByUsername($username) {
    $stmt = $this->pdo->prepare("SELECT * FROM archived_users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}






   






    
}
