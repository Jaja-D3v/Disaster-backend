<?php
require_once "../models/resetAccount.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["token"], $data["password"], $data["confirmPassword"])) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit;
}

$userModel = new User();
$response = $userModel->resetPasswordWithToken($data["token"], $data["password"], $data["confirmPassword"]);

echo json_encode($response);
