<?php
    include_once("../Database.php");

    $db = new Database();
    $connection = $db->getConnection();

    echo "Database Sucessfully Reset, you may close this tab.\n";
    
    $file = file_get_contents("db-reset.sql");
    $run = $connection->exec($file);
?>