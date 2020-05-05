<?php
    session_start();
    if (isset($_GET['id'])) {
        $url = "http://www.rjones.dev/poker-api/api/v2/games/start.php?api_key=" . $_GET['id'];
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            $res_code = curl_getinfo($ch,  CURLINFO_RESPONSE_CODE);
            if ($res_code == 201) {
                curl_close($ch);
                header("Location:games.php?d=1");
                exit();
            } else {
                $result_decoded = json_decode($result, true);
                //echo $result;
                curl_close($ch);
                //echo $result_decoded['issue'];
                header("Location: games.php?e=3&txt=" . $result_decoded['issue']);
                exit();
            }
        } else {
            $result_decoded = json_decode($result, true);
            curl_close($ch);
            echo $result_decoded['issue'];
            header("Location: games.php?e=3&txt=" . $result_decoded['issue']);
            exit();
        }
    }
?>