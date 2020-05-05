<?php
session_start();
include_once("../includes/Database.php");

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $query = "DELETE FROM games WHERE game_id=:game_id";

    try {
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bindParam(":game_id", $_GET['id']);
        }

        $result = $stmt->execute();

        if ($result) {
            header("Location: games.php?d=1");
            exit();
        }
    } catch (PDOException $e) {
        echo "DB Problem: " . $e->getMessage();
        return false;
    }
}
?>