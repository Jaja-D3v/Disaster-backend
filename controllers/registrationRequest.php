<?php
require_once "../config/db.php";
require_once "../utils/sendEmail.php";
require_once "../utils/rateLimiter.php";
require_once "../models/User.php";
require_once "../vendor/autoload.php";
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$RECAPTCHA_SECRET = $_ENV['RECAPTCHA_SECRET'] ?? null;
if (!$RECAPTCHA_SECRET) {
    echo json_encode(["success" => false, "message" => "reCAPTCHA secret not set in .env"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data["username"] ?? "");
$email = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");
$confirm_password = trim($data["confirm_password"] ?? "");
$barangay = trim($data["barangay"] ?? "");

if ($username === "" || $email === "" || $password === "" || $confirm_password === "") {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "Password and confirm password do not match."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit;
}

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d_+!@#$%^&*()-=]{8,}$/', $password)) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 8 characters and include letters and numbers."
    ]);
    exit;
}


$captchaToken = $data["g-recaptcha-response"] ?? "test muna";
if (!$captchaToken) {
    echo json_encode(["success" => false, "message" => "Missing reCAPTCHA token"]);
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'secret' => $RECAPTCHA_SECRET,
    'response' => $captchaToken,
    'remoteip' => $_SERVER['REMOTE_ADDR']
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$verifyResponse = curl_exec($ch);
curl_close($ch);

if ($verifyResponse === false) {
    echo json_encode(["success" => false, "message" => "reCAPTCHA verification failed (network error)"]);
    exit;
}

$captchaResult = json_decode($verifyResponse, true);
if (!$captchaResult["success"]) {
    echo json_encode(["success" => false, "message" => "Captcha verification failed"]);
    exit;
}


$db = (new Database())->connect();
$userModel = new User($db);


$rateLimiter = new RateLimiter($db);
$rateLimiter->checkLimit($email, 'register', 5, 1); // 5 requests per 1 hour

$archived = $userModel->isArchived($email);
if ($archived) {
    echo json_encode([
        "success" => false,
        "message" => "Registering deactivated account. Please contact administrator."
    ]);
    exit;
}


$userModel->cleanExpiredPendingRegistrations();
$checkPending = $userModel->checkPendingEmail($email);
if ($checkPending['exists']) {
    echo json_encode(["success" => false, "message" => $checkPending['message']]);
    exit;
}

$totalAccounts = $userModel->countUsers();
if ($totalAccounts >= 25) {
    echo json_encode(["success" => false, "message" => "Maximum number of accounts (25) reached."]);
    exit;
}


if ($userModel->findByUsername($username)) {
    echo json_encode(["success" => false, "message" => "Username already exists."]);
    exit;
}

if ($userModel->findByEmail($email)) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    exit;
}


$code = random_int(100000, 999999);
$expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$userModel->createPending(
    $username,
    $email,
    password_hash($password, PASSWORD_DEFAULT),
    $barangay,
    $code,
    $expires
);

$body = "Hi {$username},<br>Your verification code is: <b>{$code}</b>. It expires in 5 minutes.";
$result = sendEmail($email, "Disaster Ready - Email Verification", $body);

echo json_encode($result);
