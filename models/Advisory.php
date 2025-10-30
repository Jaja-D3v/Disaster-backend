<?php

class Advisory {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function createWeather($data) {
        $stmt = $this->pdo->prepare("INSERT INTO weather_advisories (title, details, date_time, added_by) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['added_by']]);
    }

    public function createRoad($data) {
        $stmt = $this->pdo->prepare("INSERT INTO road_advisories (title, details, date_time, status, added_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['status'], $data['added_by']]);
    }

    public function createDisaster($data) {
        $stmt = $this->pdo->prepare("INSERT INTO Disaster_update (img_path, title, details, date_time, disaster_type, added_by) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['image'], $data['title'], $data['details'], $data['dateTime'], $data['disasterType'], $data['added_by']]);
    }

    public function createCommunity($data) {
        $stmt = $this->pdo->prepare("INSERT INTO community_notice (title, details, date_time, added_by) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['added_by']]);
    }

    public function updateWeather($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE weather_advisories SET title = ?, details = ?, date_time = ?, added_by = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['added_by'], $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateRoad($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE road_advisories SET title = ?, details = ?, date_time = ?, status = ?, added_by = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['status'], $data['added_by'], $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateDisaster($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE Disaster_update SET img_path = ?, title = ?, details = ?, date_time = ?, disaster_type = ?, added_by = ? WHERE id = ?");
        $stmt->execute([$data['image'], $data['title'], $data['details'], $data['dateTime'], $data['disasterType'], $data['added_by'], $id]);
        return $stmt->rowCount() > 0;
    }

    public function updateCommunity($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE community_notice SET title = ?, details = ?, date_time = ?, added_by = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['details'], $data['dateTime'], $data['added_by'], $id]);
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
        $stmt = $this->pdo->prepare("SELECT img_path FROM Disaster_update WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && isset($data['img_path'])) {
            $imagePath = "../uploads/disasterPost/" . basename($data['img_path']);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

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
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data && isset($data['img_path'])) {
            $baseUrl = "http://localhost/disaster-backend/";
            $data['image_url'] = $baseUrl . $data['img_path'];
        }

        return $data;
    }

    public function findCommunityById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM community_notice WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAll($table) {
        $stmt = $this->pdo->prepare("SELECT * FROM $table ORDER BY id DESC");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as &$row) {
            if (isset($row['img_path'])) {
                $row['image_url'] = 'http://localhost/disaster-backend/' . $row['img_path'];
            }
        }

        return $data;
    }
}
