<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Load Composer autoload

use Dotenv\Dotenv;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    public $pdo;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'];
        $this->dbname = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
    }

    public function connect() {
        $this->pdo = null;

        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}", 
                $this->username, 
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->pdo;
    }
}
