<?php

class BarangayContact {
    protected $pdo;
    protected $table = 'barangay_contact_info';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($data) {
        $this->validateEmail($data['email']);
        $this->validateContactNumber($data['contact_number']);
        $this->validateLandline($data['landline'] ?? '');

        $sql = "INSERT INTO {$this->table} (
                    barangay_name, contact_number, landline, email, facebook_page,
                    captain_name, secretary_name, lat, `long`, date_added,
                    total_male, total_female, total_families,
                    total_male_senior, total_female_senior, total_0_4_years,
                    source, added_by
                ) VALUES (
                    :barangay_name, :contact_number, :landline, :email, :facebook_page,
                    :captain_name, :secretary_name, :lat, :long, NOW(),
                    :total_male, :total_female, :total_families,
                    :total_male_senior, :total_female_senior, :total_0_4_years,
                    :source, :added_by
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
            ':total_male' => $data['total_male'] ?? 0,
            ':total_female' => $data['total_female'] ?? 0,
            ':total_families' => $data['total_families'] ?? 0,
            ':total_male_senior' => $data['total_male_senior'] ?? 0,
            ':total_female_senior' => $data['total_female_senior'] ?? 0,
            ':total_0_4_years' => $data['total_0_4_years'] ?? 0,
            ':source' => $data['source'] ?? null,
            ':added_by' => $data['added_by'] ?? null,
        ]);
    }

    public function update($id, $data) {
        $this->validateEmail($data['email']);
        $this->validateContactNumber($data['contact_number']);
        $this->validateLandline($data['landline'] ?? '');

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
                    total_male = :total_male,
                    total_female = :total_female,
                    total_families = :total_families,
                    total_male_senior = :total_male_senior,
                    total_female_senior = :total_female_senior,
                    total_0_4_years = :total_0_4_years,
                    source = :source
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
            ':total_male' => $data['total_male'] ?? 0,
            ':total_female' => $data['total_female'] ?? 0,
            ':total_families' => $data['total_families'] ?? 0,
            ':total_male_senior' => $data['total_male_senior'] ?? 0,
            ':total_female_senior' => $data['total_female_senior'] ?? 0,
            ':total_0_4_years' => $data['total_0_4_years'] ?? 0,
            ':source' => $data['source'] ?? null,
            ':id' => $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function checkExisting($barangay_name, $email) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE barangay_name = :barangay_name AND email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':barangay_name' => $barangay_name,
            ':email' => $email
        ]);
        return $stmt->fetchColumn() > 0;
    }

    protected function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, "MX")) {
            throw new Exception("Email domain does not exist.");
        }
    }

    protected function validateContactNumber($number) {
        if (!preg_match('/^09\d{9}$/', $number)) {
            throw new Exception("Invalid Philippine mobile number. Must be 11 digits starting with 09.");
        }
    }

    protected function validateLandline($landline) {
        if (!empty($landline) && !preg_match('/^\d+$/', $landline)) {
            throw new Exception("Invalid landline number. Only digits allowed.");
        }
    }
}
