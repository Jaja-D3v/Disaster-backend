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
        return $stmt->execute([$data['img_path'], $data['title'], $data['details'], $data['dateTime'], $data['disaster_type']]);
    }

    public function createCommunity($data) {
        $stmt = $this->pdo->prepare("INSERT INTO community_notice (title, details, date_time) VALUES (?, ?, ?)");
        return $stmt->execute([$data['title'], $data['details'], $data['dateTime']]);
    }
}
