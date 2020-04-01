<?php
// Headers Required
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
// Including Models and DB
include_once '../../config/Database.php';
include_once '../../models/Games.php';
include_once '../../models/Hand.php';
include_once '../../models/Sessions.php';

$database = new Database();
$db = $database->getConnection();
$hand = new Hand($db);
$game = new Games($db);
$session = new Sessions($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->session_id) &&
    !empty($data->game_id)
) {
    if($session->validateSessionState($data->session_id)) {
        if($game->validateGameState($data->game_id)) {
            if($hand->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Hand Started", "hand_id" => $hand->hand_id));
            } else {
                http_response_code(503); 
                echo json_encode(array("message" => "Unable to start hand", "issue" => "Database Connection Issue")); 
            }
        } else {
            http_response_code(401); 
        echo json_encode(array("message" => "Unable to start hand", "issue" => "Bad game id")); 
        }
    } else {
        http_response_code(401); 
        echo json_encode(array("message" => "Unable to start hand", "issue" => "Bad session token")); 
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Unable to start hand", "issue" => "data incomplete"));
    return;
}