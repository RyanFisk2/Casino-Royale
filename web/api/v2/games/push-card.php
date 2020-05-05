<?php    
// Headers Required
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../models/Games.php';
include_once '../../models/Users.php';

$database = new Database();
$db = $database->getConnection();
$game = new Games($db);
$user = new Users($db);

// example query = {baseurl}/games/push-card.php?api_key=1&game_id=1&type=comm&card=3
// pushes 2 of hearts as a community card to the active game currently held by api_key with game_id
if (
    isset($_GET['api_key']) && 
    isset($_GET['game_id']) &&
    isset($_GET['type']) &&
    isset($_GET['card'])
) {
    $user_id = $user->fetch_id($_GET['api_key']);

    if ($game->verify($user_id, $_GET['game_id']) && $game->validateGameState($_GET['game_id'])) {
        if($_GET['card'] < 1 || $_GET['card'] > 52) {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to Push Card", "issue" => "Invalid Card Number", "valid_nums" => "1-52 (inclusive)"));
            return;   
        } else {
            if($_GET['type'] == "comm") {
                if($game->push_comm($_GET['game_id'], $_GET['card'])) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Card Pushed to Game as Community Card", "total cards scanned for game" => $game->scanned_cards));
                    return;
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Unable to Push Card", "issue" => "Internal Server Error"));
                    return;
                }
            } else if($_GET['type'] == "hand") {
                if($game->push_hand($_GET['game_id'], $_GET['card'])) {
                    http_response_code(200);
                    echo json_encode(array("message" => "Card Pushed to Game as Hand Card", "total cards scanned for game" => $game->scanned_cards));
                    return;
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "Unable to Push Card", "issue" => "Internal Server Error")); 
                    return;
                }
            } else {
                http_response_code(400);
                echo json_encode(array("message" => "Unable to Push Card", "issue" => "Invalid card type specifier", "valid_types" => "hand, comm"));
                return;
            }
        }
    } else {
        http_response_code(401); 
        echo json_encode(array("message" => "Unable to Push Card", "issue" => "Invalid API key and Game Id pair"));
        return;
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to Push Card", "issue" => "Missing Parameters", "required_params" => "api_key, game_id, type, card"));
    return;
}
