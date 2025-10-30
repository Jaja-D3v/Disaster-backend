<?php
class Location {
    private $pdo;
    private $table = "disaster_mapping";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addLocation($data) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (type, lat, lng, created_by) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['type'],
            $data['lat'],
            $data['lng'],
            $data['created_by']
        ]);
    }
    
    public function locationExists($id) {
        $stmt = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
    
    public function deleteLocation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllLocations() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
