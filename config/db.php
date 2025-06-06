<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");



class Database {
    private $host = "localhost";
    private $dbname = "disaster_system";
    private $username = "root";
    private $password = "";
    public $pdo;

  public function connect() {
        $this->pdo = null;

        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", 
                                  $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "pdoection error: " . $e->getMessage();
        }

        return $this->pdo;
    }
}