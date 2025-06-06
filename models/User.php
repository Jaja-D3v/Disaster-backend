<?php
class User {
    private $pdo;
    private $table = "users";

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
}
