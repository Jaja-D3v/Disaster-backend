<?php


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require_once "db.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Only POST requests allowed."]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if ($username === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Username and password are required."]);
    exit;
}


$query = "SELECT id, username, password, role FROM users WHERE username = :username";
$stmt = $pdo->prepare($query);
$stmt->execute(["username" => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user && $user["password"] === $password) {
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "role" => $user["role"]
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
}
