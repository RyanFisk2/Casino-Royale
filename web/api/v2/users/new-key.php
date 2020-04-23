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

    // if username does not exist
    if($user->verify()){
        if($user->new_key()) {
            // Product was created, code 201 - created
            http_response_code(201);
            echo json_encode(array("message" => "API key changed successfully", "api_key" => $user->api_key));
        } else {
            // There was an issue pushing to DB code 503 - service unavailable
            http_response_code(503);
            echo json_encode(array("message" => "Unable to change key", "issue" => "Database Connection Issue"));
        }
    } else {
        http_response_code(406);
        echo json_encode(array("message" => "Unable to change key", "issue" => "Incorrect Login Details"));
    }
    
} else {
    // Data is not complete -- response 400 bad request
    http_response_code(400);
    echo json_encode(array("message" => "Unable to change key", "issue" => "Data Incomplete"));
}
?>
