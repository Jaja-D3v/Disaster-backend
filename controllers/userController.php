<?php
require_once "../config/db.php";
require_once "../models/User.php";


$db = new Database();
$pdo = $db->connect();
$userModel = new User($pdo);

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

  
    case 'GET':
        $users = $userModel->getAllUsers(); // implement this in User.php
        echo json_encode(["success"=>true,"users"=>$users]);
        exit;
    
    

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if(!isset($input['action'],$input['target_user_id'])){
            http_response_code(400);
            echo json_encode(["success"=>false,"message"=>"Missing action or target_user_id"]);
            exit;
        }

        $action = $input['action'];
        $targetId = $input['target_user_id'];
        $superAdminId = $input['super_admin_id'] ?? null;
        $superAdminPassword = $input['super_admin_password'] ?? null;
        if($action === 'activate'){
            if(!$superAdminId || !$superAdminPassword){
                http_response_code(400);
                echo json_encode(["success"=>false,"message"=>"Super Admin verification required"]);
                exit;
            }

            $result = $userModel->activateUser($targetId, $superAdminId, $superAdminPassword);
            echo json_encode($result);
            exit;
        }


        if($action === 'deactivate'){
            if(!$superAdminId || !$superAdminPassword){
                http_response_code(400);
                echo json_encode(["success"=>false,"message"=>"Super Admin verification required"]);
                exit;
            }
            $result = $userModel->deactivateUser($targetId,$superAdminId,$superAdminPassword);
            echo json_encode($result);
            exit;
        }

        http_response_code(400);
        echo json_encode(["success"=>false,"message"=>"Invalid action"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success"=>false,"message"=>"Method not allowed"]);
        break;
}
