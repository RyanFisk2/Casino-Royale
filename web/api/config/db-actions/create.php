<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    include_once("../Database.php");

    $db = new Database();
    $connection = $db->getConnection();

    $file = file_get_contents("db-setup.sql");
    $run = $connection->exec($file);

    if($run) {
        http_response_code(201);
        echo json_encode(array("message" => "DB Created Sucessfully"));
        return;
    } else {
        $error = $connection->errorInfo();
        http_response_code(500);
        echo json_encode(array("message" => "DB Creation Unsucessful", "error" => $error[2]));
        return;
    }
    
?>