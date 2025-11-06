<?php
date_default_timezone_set('Asia/Manila');
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


        
        if (isset($user['archived_at'])) {
            unset($user['archived_at']);
        }

        
        $user['status'] = 'deactivated';

        
        $columns = implode(", ", array_keys($user));
        $placeholders = ":" . implode(", :", array_keys($user));
        $stmt = $this->pdo->prepare("INSERT INTO archived_users ($columns) VALUES ($placeholders)");
        $stmt->execute($user);

        
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        return ["success"=>true,"message"=>"User deactivated and moved to archive"];
    }



    public function activateUser($userId, $superAdminId, $superAdminPassword) {
        
        $superAdmin = $this->findById($superAdminId);
        if (!$superAdmin || $superAdmin['role'] != 1 || !password_verify($superAdminPassword, $superAdmin['password'])) {
            return ["success" => false, "message" => "Invalid Super Admin credentials"];
        }


        $user = $this->findArchivedById($userId);
        if (!$user) return ["success" => false, "message" => "Archived user not found"];

        
        if (isset($user['archived_at'])) {
            unset($user['archived_at']);
        }


        $user['status'] = 'active';

        $this->pdo->beginTransaction();
        try {
            $columns = implode(", ", array_keys($user));
            $placeholders = ":" . implode(", :", array_keys($user));

            $stmt = $this->pdo->prepare("INSERT INTO users ($columns) VALUES ($placeholders)");
            $stmt->execute($user);

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

    public function cleanExpiredPendingRegistrations() {
        $now = date('Y-m-d H:i:s');

        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->pendingTable} WHERE expires <= ?");
            $stmt->execute([$now]);
            
        } catch (PDOException $e) {
            
        }
    }

    public function checkPendingEmail($email) {
        $now = date('Y-m-d H:i:s');

        
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->pendingTable} WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $pending = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pending) {
            
            if ($pending['expires'] > $now) {
                return [
                    "exists" => true,
                    "message" => "A verification code has already been sent to this email and is still valid. Please check your inbox to continue the registration process."
                ];
            } else {
                
                $this->deletePending($pending['id']);
                return [
                    "exists" => false,
                    "message" => "Previous verification code has expired. You can request a new code."
                ];
            }
        }

        return [
            "exists" => false,
            "message" => "No pending registration found for this email."
        ];
    }


        public function isArchived($email)
        {
            $stmt = $this->pdo->prepare("SELECT * FROM archived_users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        }

        
        public function isArchivedUsername($username)
        {
            $stmt = $this->pdo->prepare("SELECT * FROM archived_users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        }

        public function isAccountPending($username)
        {
            $stmt = $this->pdo->prepare("SELECT * FROM pending_account_request WHERE username = ?");
            $stmt->execute([$username]);
            return  $stmt->fetch(PDO::FETCH_ASSOC); 
        }

        public function createAccountRequest($username, $email, $password, $role, $barangay) {
            $sql = "INSERT INTO pending_account_request (username, email, password, role, barangay) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$username, $email, $password, $role, $barangay]);
        }

        public function fetchAllPending()
        {
            $stmt = $this->pdo->query("SELECT * FROM pending_account_request ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getPendingById($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM pending_account_request WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? null;
        }

        public function approvePending($pendingUser) {
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users
                (username, email, password, role, status, barangay, last_logged_in, created_at, updated_at, login_attempts, last_attempt_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $pendingUser['username'],
                $pendingUser['email'],
                $pendingUser['password'],
                $pendingUser['role'],
                'approved',
                $pendingUser['barangay'],
                $pendingUser['last_logged_in'],
                $pendingUser['created_at'],
                date("Y-m-d H:i:s"), 
                $pendingUser['login_attempts'],
                $pendingUser['last_attempt_at']
            ]);
        }

        public function deletePendingRequest($pendingId) {
            $stmt = $this->pdo->prepare("DELETE FROM pending_account_request WHERE id = ?");
            $stmt->execute([$pendingId]);
        }


        public function blockEmail($email) {
            $stmt = $this->pdo->prepare("INSERT INTO blocked_emails (email, blocked_at) VALUES (?, NOW())");
            return $stmt->execute([$email]);
        }

        public function getBlockedEmails() {
            $stmt = $this->pdo->query("SELECT * FROM blocked_emails ORDER BY blocked_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function isEmailBlocked($email) {
            $stmt = $this->pdo->prepare("SELECT * FROM blocked_emails WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
        }

        
        public function unblockEmail($id) {
            $stmt = $this->pdo->prepare("DELETE FROM blocked_emails WHERE id = ?");
            return $stmt->execute([$id]);
        }

        public function getPendingByEmail($email) {
            $stmt = $this->pdo->prepare("SELECT * FROM pending_account_request WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getBlockedEmailById($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM blocked_emails WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? "isa";
        }




        
}
