<?php
require_once "../config/db.php";
require_once "../models/BarangayContact.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$override = $_POST['_method'] ?? json_decode(file_get_contents("php://input"), true)['_method'] ?? null;

$db = new Database();
$pdo = $db->connect();
$barangayContact = new class($pdo) extends BarangayContact {
    public function callAdd($data) {
        return $this->add($data);
    }
    public function callUpdate($id, $data) {
        return $this->update($id, $data);
    }
    public function callDelete($id) {
        return $this->delete($id);
    }
    public function callGetById($id) {
        return $this->getById($id);
    }
    public function callGetAll() {
        return $this->getAll();
    }
    public function checkExistingRecord($barangay_name, $email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM barangay_contact_info WHERE barangay_name = :barangay_name AND email = :email";
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        $stmt = $this->pdo->prepare($sql);
        $params = [
            ':barangay_name' => $barangay_name,
            ':email' => $email
        ];
        if ($excludeId) {
            $params[':id'] = $excludeId;
        }
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
};

switch (true) {
    case $method === 'GET':
        $id = $_GET['id'] ?? null;
        $result = $id ? $barangayContact->callGetById($id) : $barangayContact->callGetAll();
        echo json_encode($result);
        break;

 case $method === 'POST':
    $data = json_decode(file_get_contents("php://input"), true);

  
        if ($barangayContact->checkExistingRecord($data['barangay_name'], $data['email'])) {
            echo json_encode(["error" => "Barangay name and email already exist in the system."]);
            exit();
        }

        $saved = $barangayContact->callAdd($data);
        echo json_encode($saved
            ? ["success" => true, "message" => "Contact info added."]
            : ["error" => "Failed to add."]);
        break;
case $method === 'PUT':
    
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);


    if (!$data) {
        $data = $_POST;
    }

    $id = $_GET['id'] ?? null;

    
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(["error" => "Missing or invalid ID."]);
        exit();
    }


    $required = ['barangay_name', 'email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(["error" => "Field '$field' is required."]);
            exit();
        }
    }


    if ($barangayContact->checkExistingRecord($data['barangay_name'], $data['email'], $id)) {
        echo json_encode(["error" => "Barangay name and email already exist in the system."]);
        exit();
    }

    
    $updated = $barangayContact->callUpdate($id, $data);

    echo json_encode($updated
        ? ["success" => true, "message" => "Updated successfully."]
        : ["error" => "Update failed."]);
    break;


    case $method === 'DELETE':
        $id = $_GET['id'] ?? null;

        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "Missing or invalid ID."]);
            exit();
        }

        $deleted = $barangayContact->callDelete($id);
        echo json_encode($deleted
            ? ["success" => true, "message" => "Deleted successfully."]
            : ["error" => "Delete failed."]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Unsupported or missing _method override."]);
        break;
}
