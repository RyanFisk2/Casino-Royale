<?php
    session_start();

    if (isset($_GET['id'])) {
        $user = $_SESSION['user_id'];
        if ($user == $_GET['id']) {
            header("Location: users.php?e=1");
            exit();
        } else {
            $url = "http://www.rjones.dev/poker-api/api/v2/users/admin-delete.php?api_key=" . $_SESSION['api_key'] . "&user_id=" . $_GET['id']; 
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $result = curl_exec($ch);

            if (!curl_errno($ch)) {
                $res_code = curl_getinfo($ch,  CURLINFO_RESPONSE_CODE);
                if ($res_code == 200) {
                    curl_close($ch);
                    header("Location:users.php?d=1");
                    exit();
                } else {
                    $result_decoded = json_decode($result, true);
                    //echo $result;
                    curl_close($ch);
                    header("Location: users.php?e=3&txt=" . $result_decoded['issue']);
                    exit();
                }
            } else {
                $result_decoded = json_decode($result, true);
                curl_close($ch);
                header("Location: users.php?e=3&txt=" . $result_decoded['issue']);
                exit();
            }
        }
    } else {
        header("Location: users.php?e=2");
    }
?>