<?php
// public/index.php

// Set JSON response headers
header("Content-Type: application/json");

// Get the requested path
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = trim($request, '/');

// Map URL to actual PHP files in public/
$publicFile = __DIR__ . '/' . $request;

// If file exists in public, include it
if (file_exists($publicFile) && !is_dir($publicFile)) {
    require $publicFile;
} else {
    // Default response for root or unknown routes
    echo json_encode([
        "success" => true,
        "message" => "DisasterReady backend is running. Use /login.php, /register.php, etc."
    ]);
}
