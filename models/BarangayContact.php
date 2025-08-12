<?php

class BarangayContact {
    protected $pdo;
    protected $table = 'barangay_contact_info';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($data) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} 
            (barangay_name, contact_number, landline, email, facebook_page, captain_name, secretary_name, lat, `long`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        return $stmt->execute([
            $data['barangay_name'],
            $data['contact_number'],
            $data['landline'],
            $data['email'],
            $data['facebook_page'],
            $data['captain_name'],
            $data['secretary_name'],
            $data['lat'],
            $data['long'],
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET
            barangay_name = ?, contact_number = ?, landline = ?, email = ?, facebook_page = ?,
            captain_name = ?, secretary_name = ?, lat = ?, `long` = ?
            WHERE id = ?");

        return $stmt->execute([
            $data['barangay_name'],
            $data['contact_number'],
            $data['landline'],
            $data['email'],
            $data['facebook_page'],
            $data['captain_name'],
            $data['secretary_name'],
            $data['lat'],
            $data['long'],
            $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function checkExisting($barangay_name, $email) {
    $sql = "SELECT COUNT(*) FROM barangay_contact_info WHERE barangay_name = :barangay_name AND email = :email";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':barangay_name' => $barangay_name,
        ':email' => $email
    ]);
    return $stmt->fetchColumn() > 0; // true if exists
}

}
