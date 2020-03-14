<?php
class Users {
    // DB Connection Information
    private $conn;
    private $table_name = "users";

    // User Object Fields  
    public $id;
    public $username;
    public $password;
    public $session_active;
    public $session_id;

    // Users Object Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                    SET username=:username,password=:password,session_active=:session_active,session_id=:session_id";

        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                // $this->sanitize inputs to be safe
                $this->username=$this->sanitize($this->username);
                $this->password=$this->sanitize($this->password);
                $this->session_active=$this->sanitize($this->session_active);
                $this->session_id=$this->sanitize($this->session_id);
    
                // Bind values to prepared 
                $stmt->bindParam(":username", $this->username);
                $stmt->bindParam(":password", $this->password);
                $stmt->bindParam(":session_active",  $this->session_active);
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
        } catch (PDOException $e){
            echo "DB Problem: " . $e->getMessage();
        }   
    }

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
        }
    }

    function verify() {
        // First check username exists
        if($this->checkUsername($this->username)) {
            // Now pull the user row for verifying password
            $query = "SELECT * FROM " . $this->table_name . " WHERE username=:username";

            try {
                $stmt = $this->conn->prepare($query);

                if($stmt) {
                    $this->username = $this->sanitize($this->username);
                    $stmt->bindParam(":username", $this->username);
                }

                $result = $stmt->execute();
                if($result) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    $pw_hash = $row['password'];
                    // Password Valid
                    if(password_verify($this->password, $pw_hash)) {
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
            }
        } 
        return false;
    }

    function getIDFromUser() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username=:username";

        try {
            $stmt = $this->conn->prepare($query);

            if($stmt) {
                $this->username = $this->sanitize($this->username);
                $stmt->bindParam(":username", $this->username);
            }

            $result = $stmt->execute();

            if($result) {
                $row = $stmt->fetch();
                return $row['id'];
            } else {
                $error = $stmt->errorInfo();
                echo "Query Failed: " . $error[2] . "\n";
                return false;
            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
        }
    }

    function verifySession($session_id) {
        // to check session_id vs this->session_id
        $query = "SELECT * FROM " . $this->table_name . " WHERE id=:user_id AND session_id=:session_id";

        $stmt = $this->conn->prepare($query);

        try {
            if($stmt) {

                $this->id = $this->sanitize($this->id);
                $this->session_id = $this->sanitize($this->session_id);

                $stmt->bindParam(":user_id", $this->id);
                $stmt->bindParam(":session_id", $this->session_id);
            }

            $result = $stmt->execute();

            if($result) {
                $row = $stmt->fetch();
                if($row['session_id'] == $session_id) {
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
        }
    }

    function updateSession($session_id, $status, $user_id) {
        // Remove current session token
        if($status == 0) {
            if(verifySession($session_id)) {
                $query = "UPDATE " . $this->table_name . " SET session_active=0, session_id=0 WHERE id=:user_id"; 

                try {    
                    $stmt = $this->conn->prepare($query);
                    if($stmt) {
                        $user_id = $this->sanitize($user_id);
                        $stmt->bindParam(":username", $user_id);
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
                }
            } else {
                return false;
            }
        } else {
            $query = "UPDATE " . $this->table_name . " SET session_active=:status, session_id=:session_id WHERE id=:user_id";

            try {
                $stmt = $this->conn->prepare($query);
                if($stmt) {
                    $user_id = $this->sanitize($user_id);
                    $status = $this->sanitize($status);
                    $session_id = $this->sanitize($session_id);

                    $stmt->bindParam(":status", $status);
                    $stmt->bindParam(":session_id", $session_id);
                    $stmt->bindParam(":user_id", $user_id);
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
            }

        }
    }

    function sanitize($input) {
        return htmlspecialchars(strip_tags($input));
    }
}
?>