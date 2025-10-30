<?php
require_once "../config/db.php";
require_once "../models/User.php";

session_start(); 

date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Only POST requests allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data["username"] ?? "");
$password = trim($data["password"] ?? "");

if ($username === "" || $password === "") {
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit;
}

$db = (new Database())->connect();
$userModel = new User($db);


$user = $userModel->findByUsername($username);

if (!$user) {
    
    $archivedUser = $userModel->findArchivedByUsername($username);
    if ($archivedUser) {
        echo json_encode([
            "success" => false,
            "message" => "Your account is currently deactivated. Please reach out to the administrator to restore access."
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid username or password."
        ]);
        exit;
    }
}


$attemptData = $userModel->getUserAttempts($username);

if ($attemptData) {
    $attempts = $attemptData["login_attempts"];
    $lastAttempt = $attemptData["last_attempt_at"];
    $cooldownMinutes = 5;

    if ($attempts >= 4 && $lastAttempt) {
        $lastAttemptTime = strtotime($lastAttempt);
        $timeDiff = time() - $lastAttemptTime;

        if ($timeDiff < $cooldownMinutes * 60) {
            $remaining = ceil(($cooldownMinutes * 60 - $timeDiff) / 60);
            echo json_encode([
                "success" => false,
                "message" => "Too many failed attempts. Please try again after {$remaining} minute(s)."
            ]);
            exit;
        } else {
            $userModel->resetLoginAttempts($username);
        }
    }
}

if ($user && password_verify($password, $user["password"])) {

    $userModel->resetLoginAttempts($username);

    $_SESSION['user_id'] = $user["id"];
    $_SESSION['username'] = $user["username"];
    $_SESSION['email'] = $user["email"];
    $_SESSION['role'] = $user["role"];
    $_SESSION['barangay'] = $user["barangay"];
    $_SESSION['last_login'] = date("Y-m-d H:i:s");

    $userModel->updateLastLogin($user['id']); 
    $userModel->updateUserStatus($user['id'], 'active');

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"],
            "barangay" => $user["barangay"]
        ]
    ]);
} else {
  
    $userModel->incrementLoginAttempts($username);
    echo json_encode(["success" => false, "message" => "Invalid username or password."]);
}
