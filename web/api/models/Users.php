<?php

class Users {
    // DB Connection Information
    private $conn; 
    private $table_name = "users";
    private $game_table = "games";
    // Object Fields
    public $user_id;
    public $username;
    public $password;
    public $api_key;
    public $is_admin;
    
    // Object Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                    SET username=:username,password=:password,api_key=:api_key,is_admin=:admin";

        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                // $this->sanitize inputs to be safe
                $this->username=$this->sanitize($this->username);
                $this->password=$this->sanitize($this->password);
                $this->api_key=$this->generateKey();
    
                // Bind values to prepared 
                $stmt->bindParam(":username", $this->username);
                $stmt->bindParam(":password", $this->password);
                $stmt->bindParam(":api_key", $this->api_key);
                $stmt->bindParam(":admin", $this->is_admin);
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

    function grab_key() {

    }

    // Check to see if the API key is valid and has an active game
    function has_game($api_key) {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE api_key=:api_key";
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $stmt->bindParam(":api_key", $this->sanitize($api_key));
            }

            $result = $stmt->execute();
            if($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $user_id = $row['user_id'];                
                $query = "SELECT * FROM " . $this->game_table . "
                            WHERE created_by=:user_id";
                try {
                    $stmt = $this->conn->prepare($query);

                    if($stmt) {
                        $stmt->bindParam(":user_id", $this->sanitize($user_id));
                    }

                    $result = $stmt->execute();
                    if($result) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if($row['state'] == 1){
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

    // Grab the associated user_id for a given API key
    function fetch_id($api_key) {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE api_key=:api_key";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $api_key = $this->sanitize($api_key);
                $stmt->bindParam(":api_key", $api_key);
            }

            $result = $stmt->execute();
            if($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['user_id'];
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

    // Delete the user with the username of the object (no need to check password, has already been verified)
    function delete() {
        $query = "DELETE FROM " . $this->table_name . "
                    WHERE username=:username";
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $stmt->bindParam(":username", $this->sanitize($this->username));
            }

            $result = $stmt->execute();
            if($result) {
                if($stmt->rowCount() > 0) {
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
            echo "DB Problem: " . $e->getMessage() . "\n"; 
            return false;
        }
    }

    function id_delete($user_id) {
        $query = "DELETE FROM " . $this->table_name . "
                    WHERE user_id=:user_id";
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $stmt->bindParam(":user_id", $this->sanitize($user_id));
            }

            $result = $stmt->execute();
            if($result) {
                if($stmt->rowCount() > 0) {
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
            echo "DB Problem: " . $e->getMessage() . "\n"; 
            return false;
        }
    }
    // Verify the current username and password (unhashed) match the hash in the database.
    function verify() {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE username=:username";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $stmt->bindParam(":username", $this->sanitize($this->username));
            }

            $result = $stmt->execute();
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($this->password, $row['password'])){
                    $this->api_key = $row['api_key'];
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

    // Helper function to sanitize inputs to db
    function sanitize($input) {
        return htmlspecialchars(strip_tags($input));
    }
    
    // Generate and set a new API key for the user
    function new_key() {
        $query = "UPDATE " . $this->table_name . "
                    SET api_key=:api_key WHERE username=:username";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $new_key = $this->generateKey();

                $stmt->bindParam(":api_key", $new_key);
                $stmt->bindParam(":username", $this->username);

                $result = $stmt->execute();

                if($result) {
                    $this->api_key = $new_key;
                    return true;
                } else {
                    $error = $stmt->errorInfo();
                    echo "Query Failed: " . $error[2] . "\n";
                    return false;
                }
            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
            return false; 
        }
    }

    function id_new_key($user_id) {
        $query = "UPDATE " . $this->table_name . "
        SET api_key=:api_key WHERE user_id=:user_id";

        try {
        $stmt = $this->conn->prepare($query);

        if($stmt) {
            $new_key = $this->generateKey();

            $stmt->bindParam(":api_key", $new_key);
            $stmt->bindParam(":user_id", $user_id);

            $result = $stmt->execute();

            if($result) {
                $this->api_key = $new_key;
                return true;
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        }
        } catch (PDOException $e) {
        echo "DB Problem: " . $e->getMessage();
        return false; 
        }
    }

    // Generates a cryptographically secure API key
    function generateKey() {
        $length = 32;
        $cstrong = true;

        $bytes = openssl_random_pseudo_bytes($length, $cstrong);
        $hex = bin2hex($bytes);

        return $hex;
    }

    // Check if the inputted username exists within the database
    function checkUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username=:username";

        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $username = $this->sanitize($username);
                $stmt->bindParam(":username", $username);
            }

            $result = $stmt->execute();

            if($result) {
                $rows = $stmt->rowCount();
                if($rows > 0) {
                    return true;
                } 
                return false;
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

    function is_admin($api_key) {
        $query = "SELECT * FROM " . $this->table_name . "
                    WHERE api_key=:api_key";
        
        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $api_key = $this->sanitize($api_key);
                $stmt->bindParam(":api_key", $api_key);
            }

            $result = $stmt->execute();
            if($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['is_admin'] == 1) {
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
}

?>