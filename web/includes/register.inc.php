<?php 
    session_start();

    if (isset($_SESSION['api_key'])) {
        header("Location: ../home/index.php");
        exit();
    }

    include_once('Database.php');
    $database = new Database();
    $conn = $database->getConnection();

    if (isset($_POST['claim-submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $reppassword = $_POST['reppassword'];

        if (empty($username) || empty($password) || empty($reppassword)) {
            header("Location: ../register.php?e=1");
            exit();
        }

        if ($password != $reppassword) {
            header("Location: ../register.php?e=3");
            exit();
        }

        $query = "SELECT * FROM users WHERE username=:username";

        try {
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $username = htmlspecialchars(strip_tags($username));
                $stmt->bindParam(":username", $username);
            }

            $result = $stmt->execute();

            if ($stmt->rowCount() > 0) {
                header("Location: ../register.php?e=2");
                exit();
            } else {
                // Execute post request for creating a user
                $url = 'http://www.rjones.dev/poker-api/api/v2/users/create.php';
                $ch = curl_init($url);

                $data = array(
                    'username' => $username,
                    'password' => $password
                );

                $payload = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                $res_http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                

                if (!curl_errno($ch)) {
                    $result_decoded = json_decode($result, true);
                    curl_close($ch);
                    header("Location: ../register.php?c=1");
                    exit();
                } else {
                    echo "Error";
                    $result_decoded = json_decode($result);
                    echo $result_decoded['api_key'];
                    curl_close($ch);
                    exit();
                }


            }
        } catch (PDOException $e) {
            echo "DB Problem: " . $e->getMessage();
            exit();
        }
    }