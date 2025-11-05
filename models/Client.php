<?php
class Client {
    private $pdo;
    private $table_barangay_contact_info = "barangay_contact_info";
    private $table_weather_advisories = "weather_advisories";
    private $table_road_advisories = "road_advisories";
    private $table_community_notice = "community_notice";
    private $table_evacuation_center = "evacuation_center";
    private $table_disaster_mapping = "disaster_mapping";

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function getAllBarangayCntactInfo() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_barangay_contact_info} ORDER BY id DESC"); // goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No available data";
        }
    }

    
    public function getAllWeatherAdvisories() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_weather_advisories} ORDER BY id DESC"); //goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No available data";
        }
    }

     public function getAllRoadAdvisories() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_road_advisories} ORDER BY id DESC");  //goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No available data";
        }
    }

    public function getAllCommunityNotice() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_community_notice} ORDER BY id DESC"); // goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No available data";
        }
    }

    
    public function getAllDisasterMapping() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_disaster_mapping} ORDER BY id DESC"); // goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No data available";
        }
    }

    
    public function getAllEvacuationCenter() {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table_evacuation_center} ORDER BY id DESC"); // goods na 
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($data) {
            return $data;
        } else {
            return "No available data";
        }
    }
}
