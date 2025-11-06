<?php
require_once "../config/db.php"; 
require_once "../config/auth.php"; 
require_once "../models/User.php"; 
require_once "../utils/sendEmail.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);
    exit;
}

try {
    $db = new Database();
    $pdo = $db->connect();
    $userModel = new User($pdo);

    $requestData = json_decode(file_get_contents("php://input"), true);
    $pendingId = $requestData['id'] ?? null; 
    $password = $requestData['password'] ?? null; 
    $action = $requestData['action'] ?? null; // 'approve', 'decline', 'block', 'unblock'

    if (!$pendingId || !$action) {
        throw new Exception("Pending ID and action are required");
    }

    // Only approve, block, or unblock need admin check
    if (!in_array($action, ['decline'])) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("User not logged in");
        }

        $loggedUserId = $_SESSION['user_id'];
        $user = $userModel->getById($loggedUserId);

        if (!$user) throw new Exception("Logged-in user not found");
        if (!password_verify($password, $user['password'])) throw new Exception("Incorrect password");
        if ($user['role'] != 1) throw new Exception("User is not authorized to manage accounts");
    }

    // Prepare email and username depending on action
    if ($action === 'unblock') {
        $blockedEmail = $userModel->getBlockedEmailById($pendingId);
        if (!$blockedEmail) throw new Exception("Blocked email not found");

        $email = $blockedEmail['email'];
        $username = $blockedEmail['username'] ?? 'User';
    } else {
        $pendingUser = $userModel->getPendingById($pendingId);
        if (!$pendingUser) throw new Exception("Pending account not found");

        $email = $pendingUser['email'];
        $username = $pendingUser['username'];
    }

    // Perform the action
    switch ($action) {
        case 'approve':
            $userModel->approvePending($pendingUser);
            $userModel->deletePendingRequest($pendingId);

            $body = "Hi {$username},<br><br>
            We are pleased to inform you that your account has been <b>approved</b> and is now <b>active</b>.<br><br>
            You can now log in using your registered credentials.<br><br>
            Best regards,<br>Disaster Ready Team";

            sendEmail($email, "Disaster Ready - Account Approved", $body);
            $message = "Pending account approved and moved to users table";
            break;

        case 'decline':
            $userModel->deletePendingRequest($pendingId);

            $body = "Hi {$username},<br><br>
            We regret to inform you that your account request has been <b>declined</b>.<br><br>
            Best regards,<br>Disaster Ready Team";

            sendEmail($email, "Disaster Ready - Account Declined", $body);
            $message = "Pending account declined and removed";
            break;

        case 'block':
            $userModel->deletePendingRequest($pendingId);
            $userModel->blockEmail($email);

            $body = "Hi {$username},<br><br>
            Your account request has been <b>blocked</b>. This email cannot request an account again.<br><br>
            Best regards,<br>Disaster Ready Team";

            sendEmail($email, "Disaster Ready - Email Blocked", $body);
            $message = "Pending account blocked and email added to blocklist";
            break;

        case 'unblock':
            $userModel->unblockEmail($pendingId);

            $body = "Hi {$username},<br><br>
            Your email <b>{$email}</b> has been <b>unblocked</b> by the Disaster Ready administration.<br>
            You may now request for a new account again.<br><br>
            Best regards,<br>Disaster Ready Team";

            sendEmail($email, "Disaster Ready - Email Unblocked", $body);
            $message = "Email successfully unblocked and removed from blocklist";
            break;

        default:
            throw new Exception("Invalid action");
    }

    echo json_encode(["success" => true, "message" => $message]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
