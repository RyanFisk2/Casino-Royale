<?php
// Headers Required
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Include DB Connection and User Model
include_once '../../config/Database.php';
include_once '../../models/Users.php';

// Create db instance/connection and product obj
$database = new Database();
$db = $database->getConnection();
$user = new Users($db);

// Get JSON Data from POST
$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->username) && 
    !empty($data->password) 
) {
    // Data is complete, process it
    $user->username = $data->username;
    $user->password = $data->password;

    if($user->verify()) {
        if($user->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "User deleted successfully"));
            return;
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to remove User", "issue" => "Database Connection Issue"));
            return;
        }
    } else {
        http_response_code(403);
        echo json_encode(array("message" => "Unable to remove User", "issue" => "Incorrect Login Details"));
        return;
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to remove User", "issue" => "Data Incomplete"));
    return;
}
?>
