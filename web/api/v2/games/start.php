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
include_once '../../models/Users.php';

$database = new Database();
$db = $database->getConnection();
$game = new Games($db);
$user = new Users($db);

// If there is an API key in the URL
if(isset($_GET['api_key'])){
    $key = $_GET['api_key'];
    // Check if there is an active game for the user assoc. with the key
    if(!$user->has_game($key)){
        $game->created_by = $user->fetch_id($key);
        if($game->create()) {
            http_response_code(201);
            echo json_encode(array("message" => "Game Started", "game_id" => $game->game_id));
            return; 
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to Start Game", "issue" => "Database Connection Issue"));
            return;
        }
    } else {
        http_response_code(409);
        echo json_encode(array("message" => "Unable to Start Game", "issue" => "Key already has an active game"));
        return;
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to Start Game", "issue" => "No API Key Supplied"));
    return;
}
