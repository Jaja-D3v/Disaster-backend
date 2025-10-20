<?php
class Incident {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getIncidents($lastFetch = 0) {
        $stmt = $this->pdo->prepare("SELECT * FROM incident_reports WHERE UNIX_TIMESTAMP(created_at) > ? ORDER BY created_at DESC");
        $stmt->execute([$lastFetch]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createIncident($reporter_name, $reporter_contact, $description, $media_path = null) {
        $stmt = $this->pdo->prepare("INSERT INTO incident_reports (reporter_name, reporter_contact, description, media) VALUES (?, ?, ?, ?)");
        $stmt->execute([$reporter_name, $reporter_contact, $description, $media_path]);
        return $this->pdo->lastInsertId();
    }

    public function updateIncident($id, $status, $responded_by = null) {
        $stmt = $this->pdo->prepare("UPDATE incident_reports SET status = ?, responded_by = ? WHERE id = ?");
        return $stmt->execute([$status, $responded_by, $id]);
    }
}
