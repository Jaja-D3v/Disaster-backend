<?php
require_once "../config/db.php";
require_once "../models/DisasterMapping.php";

header("Content-Type: application/json");

// create database connection
$database = new Database();
$pdo = $database->connect();

// pass connection to model
$location = new Location($pdo);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    if ($location->addLocation($input)) {
        echo json_encode(["success" => true, "message" => "Location added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add location"]);
    }

} elseif ($method === "DELETE") {
    $id = $_GET['id'] ?? null;

    if ($id && $location->deleteLocation($id)) {
        echo json_encode(["success" => true, "message" => "Location deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete location"]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
