<?php
    session_start();

    if (isset($_GET['id'])) {
        $api_key = $_SESSION['api_key'];
        $user_id = $_GET['id'];

        $url = "http://www.rjones.dev/poker-api/api/v2/users/admin-new-key.php?api_key=". $api_key ."&user_id=" . $user_id;
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            //echo curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $result_decoded = json_decode($result, true);
            curl_close($ch); 
            if ($user_id == $_SESSION['user_id']) {
                $_SESSION['api_key'] = $result_decoded['new_key'];
            }
            header("Location: users.php");
            exit();
        } else {
            echo "ERROR\n";
            $result_decoded = json_decode($result, true);
            echo $result_decoded['issue'];
            curl_close($ch);
            exit();
        }
    }

?>