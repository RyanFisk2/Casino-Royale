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
$game = new Games($db);
$session = new Sessions($db);

$data = json_decode(file_get_contents("php://input"));

// Session id is present
if(!empty($data->session_id)) {
    // If session is valid and active
    if($session->validateSessionState($data->session_id)) {
        if($game->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Game Started", "game_id" => $game->game_id));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to start game", "issue" => "Database Connection Issue"));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Unable to start Game", "issue" => "Bad Session Token"));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Unable to start Game", "issue" => "Missing Session Token"));
}