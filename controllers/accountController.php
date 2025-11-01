<?php
require_once "../config/db.php";
require_once "../models/Account.php";

$database = new Database();
$pdo = $database->connect();
$account = new Account($pdo);

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'])) {
    echo json_encode(["success" => false, "message" => "No action specified."]);
    exit;
}

$userId = $data['id'] ?? null;

switch ($data['action']) {
    case "updateProfile":
        if (!isset($data['username'], $data['email'], $data['password'])) {
            echo json_encode(["success" => false, "message" => "Missing fields."]);
            exit;
        }

        $user = $account->getUserById($userId);
        if (!$user) {
            echo json_encode(["success" => false, "message" => "User not found."]);
            exit;
        }

        if (!password_verify($data['password'], $user['password'])) {
            echo json_encode(["success" => false, "message" => "Password is incorrect."]);
            exit;
        }

        if ($account->isEmailTaken($data['email'], $userId)) {
            echo json_encode(["success" => false, "message" => "Email is already in use."]);
            exit;
        }

        if ($data['username'] === $user['username'] && $data['email'] === $user['email']) {
            echo json_encode(["success" => false, "message" => "No changes made."]);
            exit;
        }

        $updated = $account->updateProfile($userId, $data['username'], $data['email']);
        echo json_encode([
            "success" => $updated,
            "message" => $updated ? "Profile updated successfully." : "Failed to update profile."
        ]);
        break;

    case "changePassword":
        if (!isset($data['currentPassword'], $data['newPassword'], $data['confirmPassword'])) {
            echo json_encode(["success" => false, "message" => "Missing fields."]);
            exit;
        }

        $user = $account->getUserById($userId);
        if (!$user) {
            echo json_encode(["success" => false, "message" => "User not found."]);
            exit;
        }

        if (!password_verify($data['currentPassword'], $user['password'])) {
            echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
            exit;
        }

        if ($data['newPassword'] !== $data['confirmPassword']) {
            echo json_encode(["success" => false, "message" => "New password and confirm password do not match."]);
            exit;
        }

        if (password_verify($data['newPassword'], $user['password'])) {
            echo json_encode(["success" => false, "message" => "No changes made. The new password cannot be the same as the current one."]);
            exit;
        }

        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $data['newPassword'])) {
            echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long and include uppercase, lowercase, and a number."]);
            exit;
        }

        $history = $account->getPasswordHistory($userId);
        foreach ($history as $old) {
            if (password_verify($data['newPassword'], $old['password'])) {
                $usedDate = new DateTime($old['created_at']);
                $now = new DateTime();
                $interval = $usedDate->diff($now);
                if ($interval->m + ($interval->y * 12) < 5) {
                    echo json_encode(["success" => false, "message" => "This password was already used within the last 5 months. Please choose a different one."]);
                    exit;
                }
            }
        }

        $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);
        $updated = $account->updatePassword($userId, $hashedPassword);
        if ($updated) {
            $account->addPasswordToHistory($userId, $hashedPassword);
            echo json_encode(["success" => true, "message" => "Password changed successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to change password."]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Invalid action."]);
}
