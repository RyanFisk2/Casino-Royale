<?php
class Games {
    // DB Connection
    private $conn;
    private $table_name = "games";

    // Games Fields
    public $game_id;
    public $session_id;
    public $is_active;
    public $hand_id;
     
    // Object constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . "
                    SET game_id=:game_id, session_id=:session_id, is_active=1";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $this->session_id = $this->sanitize($this->session_id);
                $this->game_id = $this->generateID(); 

                $stmt->bindParam(":game_id", $this->game_id);
                $stmt->bindParam(":session_id", $this->session_id);
            }

            $result = $stmt->execute();

            if($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
            return false; 
        }
    }

    function validateGameState($game_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE game_id=:game_id";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $game_id = $this->sanitize($game_id);
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();
            if($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['is_active'] == 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
            return false;
        }
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
}
