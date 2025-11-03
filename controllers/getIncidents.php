<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/incident.php';

$db = new Database();
$pdo = $db->connect();

$incidentModel = new Incident($pdo);

$lastFetch = $_GET['last_fetch'] ?? 0;
$incidents = $incidentModel->getIncidents($lastFetch);

echo json_encode($incidents);
