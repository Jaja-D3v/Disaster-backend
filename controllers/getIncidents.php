<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/incident.php';

$incidentModel = new Incident($pdo);

$lastFetch = $_GET['last_fetch'] ?? 0;
$incidents = $incidentModel->getIncidents($lastFetch);

header('Content-Type: application/json');
echo json_encode($incidents);
