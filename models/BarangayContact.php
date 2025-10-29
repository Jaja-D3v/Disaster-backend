<?php

class BarangayContact {
    protected $pdo;
    protected $table = 'barangay_contact_info';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ✅ Add new record with created_by and created_at
    public function add($data) {
        $sql = "INSERT INTO {$this->table} (
                    barangay_name, contact_number, landline, email, facebook_page, 
                    captain_name, secretary_name, lat, `long`, created_by, created_at
                ) VALUES (
                    :barangay_name, :contact_number, :landline, :email, :facebook_page,
                    :captain_name, :secretary_name, :lat, :long, :created_by, NOW()
                )";
                
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':barangay_name' => $data['barangay_name'],
            ':contact_number' => $data['contact_number'],
            ':landline' => $data['landline'],
            ':email' => $data['email'],
            ':facebook_page' => $data['facebook_page'],
            ':captain_name' => $data['captain_name'],
            ':secretary_name' => $data['secretary_name'],
            ':lat' => $data['lat'],
            ':long' => $data['long'],
            ':created_by' => $data['created_by'] ?? null,
        ]);
    }

    // ✅ Update existing record with updated_by and updated_at
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET
                    barangay_name = :barangay_name,
                    contact_number = :contact_number,
                    landline = :landline,
                    email = :email,
                    facebook_page = :facebook_page,
                    captain_name = :captain_name,
                    secretary_name = :secretary_name,
                    lat = :lat,
                    `long` = :long,
                    updated_by = :updated_by,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':barangay_name' => $data['barangay_name'],
            ':contact_number' => $data['contact_number'],
            ':landline' => $data['landline'],
            ':email' => $data['email'],
            ':facebook_page' => $data['facebook_page'],
            ':captain_name' => $data['captain_name'],
            ':secretary_name' => $data['secretary_name'],
            ':lat' => $data['lat'],
            ':long' => $data['long'],
            ':updated_by' => $data['updated_by'] ?? null,
            ':id' => $id,
        ]);
    }

    // ✅ Delete record
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ✅ Get all records
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Get one record by ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Check for existing barangay/email combo (prevent duplicates)
    protected function checkExisting($barangay_name, $email) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE barangay_name = :barangay_name AND email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':barangay_name' => $barangay_name,
            ':email' => $email
        ]);
        return $stmt->fetchColumn() > 0; // true if exists
    }
}
