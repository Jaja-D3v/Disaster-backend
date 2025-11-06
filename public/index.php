<?php
// Minimal entry point for DisasterReady backend

// Set JSON response headers
header("Content-Type: application/json");

// Simple check endpoint
echo json_encode([
    "success" => true,
    "message" => "DisasterReady backend is running. Access your endpoints via your existing scripts in /controllers."
]);
