<?php
//include_once '../config/CardMap.php';

class Games {
    // DB Connection
    private $conn;
    private $table_name = "games";
    private $user_table = "users";

    // Games Fields
    public $game_id;
    public $created_by;
    public $state; 
    public $scanned_cards;
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

    // Object constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    function create($id) {
        $query = "INSERT INTO " . $this->table_name . "
                    SET game_id=:game_id, created_by=:created_by, state=1";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $this->game_id = $this->generateID(); 

                $stmt->bindParam(":game_id", $this->game_id);
                $stmt->bindParam(":created_by", $id);
            }

            $result = $stmt->execute();

            if($result) {
                return game_id;
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
                if($row['state'] == 1) {
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

    function verify($user_id, $game_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE game_id=:game_id";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $game_id = $this->sanitize($game_id);
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();
            if ($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['created_by'] == $user_id) {
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

    function push_comm($game_id, $card) {
        $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE game_id=:game_id";
        
        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();
            if ($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $scanned = $row['scanned_cards'];
                if ($scanned >= 7) {
                    return false;
                }
                $in_hand = $this->count_hand($row['hand_1'], $row['hand_2']);
                $query = "";
                if ($in_hand == 0) {
                    switch ($scanned) {
                        case 0:
                            $query = "UPDATE " . $this->table_name . " SET comm_1=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 1:
                            $query = "UPDATE " . $this->table_name . " SET comm_2=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 2:
                            $query = "UPDATE " . $this->table_name . " SET comm_3=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;

                        case 3:
                            $query = "UPDATE " . $this->table_name . " SET comm_4=:card, scanned_cards=:scanned WHERE game_id=:game_id"; 
                            break;
                        
                        case 4: 
                            $query = "UPDATE " . $this->table_name . " SET comm_5=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        default:
                            $query = "ERROR";
                            break;
                    }
                } else if ($in_hand == 1) {
                    switch ($scanned) {
                        case 1:
                            $query = "UPDATE " . $this->table_name . " SET comm_1=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 2:
                            $query = "UPDATE " . $this->table_name . " SET comm_2=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 3:
                            $query = "UPDATE " . $this->table_name . " SET comm_3=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;

                        case 4:
                            $query = "UPDATE " . $this->table_name . " SET comm_4=:card, scanned_cards=:scanned WHERE game_id=:game_id"; 
                            break;
                        
                        case 5: 
                            $query = "UPDATE " . $this->table_name . " SET comm_5=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        default:
                            $query = "ERROR";
                            break;
                    }
                } else {
                    switch ($scanned) {
                        case 2:
                            $query = "UPDATE " . $this->table_name . " SET comm_1=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 3:
                            $query = "UPDATE " . $this->table_name . " SET comm_2=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 4:
                            $query = "UPDATE " . $this->table_name . " SET comm_3=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;

                        case 5: 
                            $query = "UPDATE " . $this->table_name . " SET comm_4=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        case 6: 
                            $query = "UPDATE " . $this->table_name . " SET comm_5=:card, scanned_cards=:scanned WHERE game_id=:game_id";
                            break;
                        
                        default:
                            $query = "ERROR";
                            break;
                    }
                }

                if ($query == "ERROR") {
                    return false;
                }

                try {
                    $stmt = $this->conn->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bindParam(":game_id", $game_id);
                        $stmt->bindParam(":card", $this->cardTxt($card));
                        $new_count = $scanned + 1;
                        $stmt->bindParam(":scanned", $new_count);
                    }
                    
                    $result = $stmt->execute();
                    if ($result) {
                        $this->scanned_cards = $scanned + 1;
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

    function push_hand($game_id, $card) {
        $query = "SELECT * FROM " . $this->table_name . " 
                    WHERE game_id=:game_id";
        //echo $card_map[$card];
        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();
            if ($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $scan_count = $row['scanned_cards'];
                if ($scan_count >= 7) {
                    return false;
                }

                if (($row['hand_1'] != "NC") && ($row['hand_2'] != "NC")) {
                    // Both cards have been filled in, can't push
                    return false;
                } else if (($row['hand_1'] != "NC")) {
                    // first has been filled, push to second
                    $query = "UPDATE " . $this->table_name . " SET hand_2=:hand_2, scanned_cards=:scanned 
                                WHERE game_id=:game_id";
                    
                    try {
                        $stmt = $this->conn->prepare($query);
                        
                        if ($stmt) {
                            $stmt->bindParam(":hand_2", $this->cardTxt($card));
                            $new_count = $scan_count + 1;
                            $stmt->bindParam(":scanned", $new_count);
                            $stmt->bindParam(":game_id", $game_id);
                        }

                        $result = $stmt->execute();
                        
                        if ($result) {
                            $this->scanned_cards = $scan_count + 1;
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
                } else {
                    // neither have been filled, push to first
                    $query = "UPDATE " . $this->table_name . " SET hand_1=:hand_1, scanned_cards=:scanned 
                    WHERE game_id=:game_id";
                    
                    try {
                        $stmt = $this->conn->prepare($query);

                        if ($stmt) {
                            $stmt->bindParam(":hand_1", $this->cardTxt($card));
                            $new_count = $scan_count + 1;
                            $stmt->bindParam(":scanned", $new_count);
                            $stmt->bindParam(":game_id", $game_id);
                        }

                        $result = $stmt->execute();

                        if ($result) {
                            $this->scanned_cards = $scan_count + 1;
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

    function push_odds($game_id, $value) {
        $query = "UPDATE " . $this->table_name . " SET odds=:odds WHERE game_id=:game_id";

        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $game_id = $this->sanitize($game_id);
                $value = $this->sanitize($value);

                $stmt->bindParam(":odds", $value);
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e){
            echo "DB Problem: " . $e->getMessage();
            return false;
        }
    }

    function push_score($game_id, $value) {
        $query = "UPDATE " . $this->table_name . " SET score=:score WHERE game_id=:game_id";

        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $game_id = $this->sanitize($game_id);
                $value = $this->sanitize($value);

                $stmt->bindParam(":score", $value);
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e){
            echo "DB Problem: " . $e->getMessage();
            return false;
        }
    }

    function push_avg($game_id, $value) {
        $query = "UPDATE " . $this->table_name . " SET avg_score=:avg WHERE game_id=:game_id";

        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $game_id = $this->sanitize($game_id);
                $value = $this->sanitize($value);

                $stmt->bindParam(":avg", $value);
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e){
            echo "DB Problem: " . $e->getMessage();
            return false;
        }
    }

    function end($game_id) {
        $query = "UPDATE " . $this->table_name . " SET state=0 WHERE game_id=:game_id";

        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
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

    function populate_data($game_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE game_id=:game_id";

        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt) {
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->game_id = $game_id;
                $this->created_by = $row['created_by'];
                $this->state = $row['state'];
                $this->scanned_cards = $row['scanned_cards'];
                $this->comm_1 = $row['comm_1'];
                $this->comm_2 = $row['comm_2'];
                $this->comm_3 = $row['comm_3'];
                $this->comm_4 = $row['comm_4'];
                $this->comm_5 = $row['comm_5'];
                $this->hand_1 = $row['hand_1'];
                $this->hand_2 = $row['hand_2'];
                $this->score = $row['score'];
                $this->odds = $row['odds'];
                $this->avg_score = $row['avg_score'];

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

    function data_array() {
        $data = array (
            "game_id" => $this->game_id,
            "created_by" => $this->created_by,
            "state" => $this->state,
            "scanned_cards" => $this->scanned_cards,
            "comm_1" => $this->comm_1,
            "comm_2" => $this->comm_2,
            "comm_3" => $this->comm_3,
            "comm_4" => $this->comm_4,
            "comm_5" => $this->comm_5,
            "hand_1" => $this->hand_1,
            "hand_2" => $this->hand_2,
            "score" => $this->score,
            "odds" => $this->odds,
            "avg_score" => $this->odds,
        );

        return $data;
    }

    function count_hand($hand_1, $hand_2) {
        $total = 0;

        if ($hand_1 != "NC") {
            $total = $total + 1;
        }
        if ($hand_2 != "NC") {
            $total = $total + 1;
        }

        return $total;
    }

    function generateID() {
        $length = 10;
        $cstrong = true;

        $bytes = openssl_random_pseudo_bytes($length, $cstrong);
        $hex = bin2hex($bytes);

        return $hex;
    }

    function cardTxt($input) {
        $card_map = array( 
            "2C" => 1,
            "2D" => 2,
            "2H" => 3,
            "2S" => 4,
            "3C" => 5,
            "3D" => 6,
            "3H" => 7,
            "3S" => 8,
            "4C" => 9,
            "4D" => 10,
            "4H" => 11,
            "4S" => 12,
            "5C" => 13,
            "5D" => 14,
            "5H" => 15,
            "5S" => 16,
            "6C" => 17,
            "6D" => 18,
            "6H" => 19,
            "6S" => 20,
            "7C" => 21,
            "7D" => 22,
            "7H" => 23,
            "7S" => 24,
            "8C" => 25,
            "8D" => 26,
            "8H" => 27,
            "8S" => 28,
            "9C" => 29,
            "9D" => 30,
            "9H" => 31,
            "9S" => 32,
            "TC" => 33,
            "TD" => 34,
            "TH" => 35,
            "TS" => 36,
            "JC" => 37,
            "JD" => 38,
            "JH" => 39,
            "JS" => 40,
            "QC" => 41,
            "QD" => 42,
            "QH" => 43,
            "QS" => 44,
            "KC" => 45,
            "KD" => 46,
            "KH" => 47,
            "KS" => 48,
            "AC" => 49,
            "AD" => 50,
            "AH" => 51,
            "AS" => 52);

            return array_search($input, $card_map);
    }

    function sanitize($input) {
        return htmlspecialchars(strip_tags($input)); 
    }
}
