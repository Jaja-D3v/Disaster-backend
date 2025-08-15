<?php
class EvacuationCenter {
    private $pdo;
    private $table = 'evacuation_center';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

   public function add($data) {
    $sql = "INSERT INTO {$this->table} 
        (name, location, capacity, current_evacuees, contact_person, contact_number, lat, `long`, created_by) 
        VALUES (:name, :location, :capacity, :current_evacuees, :contact_person, :contact_number, :lat, :long, :created_by)";
    
    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':name' => $data['evac_name'],
        ':location' => $data['evac_location'],
        ':capacity' => $data['evac_capacity'],
        ':current_evacuees' => $data['evac_evacuees'],
        ':contact_person' => $data['evac_contact_person'],
        ':contact_number' => $data['evac_contact_number'],
        ':lat' => $data['lat'],
        ':long' => $data['long'],
        ':created_by' => $data['created_by']
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
        `long` = :long,
        created_by = :created_by
        WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':name' => $data['evac_name'],
        ':location' => $data['evac_location'],
        ':capacity' => $data['evac_capacity'],
        ':current_evacuees' => $data['evac_evacuees'],
        ':contact_person' => $data['evac_contact_person'],
        ':contact_number' => $data['evac_contact_number'],
        ':lat' => $data['lat'],
        ':long' => $data['long'],
        ':created_by' => $data['created_by'],
        ':id' => $id
    ]);
}


    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
