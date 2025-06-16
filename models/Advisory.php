<?php

class Advisory {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createWeather($data) {
        $stmt = $this->pdo->prepare("INSERT INTO weather_advisories (title, details, date_time) VALUES (?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime']]);
    }

    public function createRoad($data) {
        $stmt = $this->pdo->prepare("INSERT INTO road_advisories (title, details, date_time, status) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['status']]);
    }

    public function createDisaster($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Disaster_update (img_path, title, details, date_time, disaster_type) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['image'], $data['title'], $data['details'], $data['dateTime'], $data['disasterType']]);
    }

    public function createCommunity($data) {
        $stmt = $this->pdo->prepare("INSERT INTO community_notice (title, details, date_time) VALUES (?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime']]);
    }

    public function updateWeather($id, $data) {
    $stmt = $this->pdo->prepare("UPDATE weather_advisories SET title = ?, details = ?, date_time = ? WHERE id = ?");
    $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $id]);
    return $stmt->rowCount() > 0;
    }

    public function updateRoad($id, $data) {
        
        if (!isset($data['title'], $data['details'], $data['dateTime'], $data['status'])) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE road_advisories SET title = ?, details = ?, date_time = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $data['title'],
            $data['details'],
            $data['dateTime'],
            $data['status'],
            $id
        ]);

        
        return $stmt->rowCount() > 0;
    }


    public function updateDisaster($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Disaster_update SET img_path = ?, title = ?, details = ?, date_time = ?, disaster_type = ? WHERE id = ?");
        $stmt->execute([$data['image'], $data['title'], $data['details'], $data['dateTime'], $data['disasterType'], $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateCommunity($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE community_notice SET title = ?, details = ?, date_time = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $id]);
        return $stmt->rowCount() > 0;
    }


        public function deleteWeather($id) {
    $stmt = $this->pdo->prepare("DELETE FROM weather_advisories WHERE id = ?");
    return $stmt->execute([$id]);
    }

    public function deleteRoad($id) {
        $stmt = $this->pdo->prepare("DELETE FROM road_advisories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteDisaster($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Disaster_update WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteCommunity($id) {
        $stmt = $this->pdo->prepare("DELETE FROM community_notice WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findWeatherById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM weather_advisories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


    public function findRoadById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM road_advisories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findDisasterById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Disaster_update WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findCommunityById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM community_notice WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

        public function getAll($table) {
        $stmt = $this->pdo->query("SELECT * FROM {$table} ORDER BY date_time DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
