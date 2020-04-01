<?php

class Hand {
    
    private $conn;
    private $table = "hand";

    public $hand_id;
    public $game_id;
    public $comm_1;
    public $comm_2;
    public $comm_3;
    public $comm_4;
    public $comm_5;
    public $hand_1;
    public $hand_2; 
    public $score;
    public $odds;
    public $avg_score;

    public function __construct($db) {
        $this->conn = $db;
    }

    function generateID() {
        $length = 10;
        $cstrong = true;

        $bytes = openssl_random_pseudo_bytes($length, $cstrong);
        $hex = bin2hex($bytes);

        return $hex;
    }

    function sanitize($input) {
        return htmlspecialchars(strip_tags($input));
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                    SET hand_id=:hand_id, game_id=:game_id";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $this->game_id = $this->sanitize($this->game_id);
                $this->hand_id = $this->generateID();

                $stmt->bindParam(":hand_id", $this->hand_id);
                $stmt->bindParam(":game_id", $this->game_id);
            }

            $result = $stmt->execute();

            if($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: ". $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
            return false;
        }
    }
}
