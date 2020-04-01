<?php
// Headers Required
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Include DB Connection and User Model
include_once '../../config/Database.php';
include_once '../../models/Sessions.php';
include_once '../../models/Users.php';

$database = new Database();
$db = $database->getConnection();
$session = new Sessions($db);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->username) &&
    !empty($data->password)
) {
    $test_user = new Users($db);
    $test_user->username = $data->username;
    $test_user->password = $data->password;

    $verify = $test_user->verify();

    if($verify) {
        $id = $test_user->getIDFromUser();
        $session->created_by = $id;

        if($session->checkUserSessionState() == 0) {
            // User already has session, code 409 - conflict
            http_response_code(409);
            echo json_encode(array("message" => "Unable to end Session", "issue" => "User does not have active session!"));
        }
        if($session->start()) {
            if($test_user->updateSession($session->session_id, 0, $session->created_by)) {
                // Session Started, code 201 - created
                http_response_code(201);
                echo json_encode(array("message" => "Session ended Successfully"));
            } else {
                // Session Started, user not updated code 500 - Internal server error
                http_response_code(500);
                echo json_encode(array("message" => "Unable to end Session", "issue" => "Unable to remove Session from User"));
            }
            
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to end Session", "issue" => "Database Connection Issue"));
        }

    } else {
        // Login data bad.
        http_response_code(403);
        echo json_encode(array("message" => "Unable to end Session", "issue" => "Incorrect Login Details"));
    }
} else {
    // No login data present
    http_response_code(400);
    echo json_encode(array("message" => "Unable to end Session", "issue" => "Data Incomplete"));
}