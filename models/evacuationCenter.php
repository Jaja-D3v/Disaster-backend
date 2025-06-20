<?php
class EvacuationCenter {
    private $pdo;
    private $table = 'evacuation_center';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function add($data) {
        $sql = "INSERT INTO {$this->table} 
            (name, location, capacity, current_evacuees, contact_person, contact_number, lat, `long`) 
            VALUES (:name, :location, :capacity, :current_evacuees, :contact_person, :contact_number, :lat, :long)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':location' => $data['location'],
            ':capacity' => $data['capacity'],
            ':current_evacuees' => $data['current_evacuees'],
            ':contact_person' => $data['contact_person'],
            ':contact_number' => $data['contact_number'],
            ':lat' => $data['lat'],
            ':long' => $data['long']
        ]);
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

    public function update($id, $data) {
    $sql = "UPDATE {$this->table} SET 
        name = :name,
        location = :location,
        capacity = :capacity,
        current_evacuees = :current_evacuees,
        contact_person = :contact_person,
        contact_number = :contact_number,
        lat = :lat,
        `long` = :long
        WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':name' => $data['name'],
        ':location' => $data['location'],
        ':capacity' => $data['capacity'],
        ':current_evacuees' => $data['current_evacuees'],
        ':contact_person' => $data['contact_person'],
        ':contact_number' => $data['contact_number'],
        ':lat' => $data['lat'],
        ':long' => $data['long'],
        ':id' => $id
    ]);
}


    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
