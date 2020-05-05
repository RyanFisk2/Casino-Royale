<?php
    include_once("../includes/Database.php");
    $game_id = "";

    $refresh_tag = "";
    $comm_1 = "";
    $comm_2 = "";
    $comm_3 = "";
    $comm_4 = "";
    $comm_5 = "";
    $hand_1 = "";
    $hand_2 = "";
    $scanned_cards = -1;
    $score = 0;
    $odds = 0.0;
    $avg_score = 0;

    if (isset($_GET['id'])) {
        $game_id = $_GET['id'];
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT * FROM games WHERE game_id=:game_id";

        try {
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bindParam(":game_id", $game_id);
            }

            $result = $stmt->execute();

            if ($result) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $hand_1 = $row['hand_1'];
                $hand_2 = $row['hand_2'];
                $comm_1 = $row['comm_1'];
                $comm_2 = $row['comm_2'];
                $comm_3 = $row['comm_3'];
                $comm_4 = $row['comm_4'];
                $comm_5 = $row['comm_5'];
                $scanned_cards = $row['scanned_cards'];
                $score = $row['score'];
                $odds = $row['odds'];
                $avg_score = $row['avg_score'];
                $created_by = $row['created_by'];
            } else {

            }
        } catch (PDOException $e) {

        }

        if (($scanned_cards != -1) && ($scanned_cards < 7)) {
            include_once('refresh-header.php');
        } else {
            include_once('header.php');
        }
    }

?>
<div class="container-fluid">
    <div class="row mx-4 my-4" style="border: 5px solid black;">
        <div class="col">
            <h1 class="mx-5 my-5">Cards on the Table:</h1>
        </div>
        <div class="col">
            <div class="card">
                <img src='../assets/cards/<?php echo $comm_1; ?>.png' class="card-img-top">
            </div>
        </div>
        <div class="col">
            <div class="card">
                <img src='../assets/cards/<?php echo $comm_2; ?>.png' class="card-img-top">
            </div>
        </div>
        <div class="col">
            <div class="card">
                <img src='../assets/cards/<?php echo $comm_3; ?>.png' class="card-img-top">
            </div>
        </div>
        <div class="col">
            <div class="card">
                <img src='../assets/cards/<?php echo $comm_4; ?>.png' class="card-img-top">
            </div>
        </div>
        <div class="col">
            <div class="card">
                <img src='../assets/cards/<?php echo $comm_5; ?>.png' class="card-img-top">
            </div>
        </div>
    </div>
    <div class="row mx-4 my-4">
            <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-left-style:solid; border-bottom-style:solid;">
                <h1 class="mx-5 my-5">Cards in the Hand:</h1>
            </div>
            <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-bottom-style:solid;">
                <div class="card">
                    <img src='../assets/cards/<?php echo $hand_1; ?>.png' class="card-img-top">
                </div>
            </div>
            <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-right-style:solid; border-bottom-style:solid;">
                <div class="card">
                    <img src='../assets/cards/<?php echo $hand_2; ?>.png' class="card-img-top">
                </div>
            </div>
        <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-left-style:solid; border-bottom-style:solid;">
            <h1 class="mx-5 my-5">Game Statistics:</h1>
        </div>
        <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-bottom-style:solid;">
            <h4 class="mt-5 my-2">Hand Score: <?php echo $score; ?></h4>
            <br />
            <h4 class="mt-4 my-2">Avg Score: <?php echo $avg_score; ?></h4>
        </div>
        <div class="col" style="border-width: 5px; border-color:black; border-top-style:solid; border-right-style:solid; border-bottom-style:solid;">
            <h4 class="mt-5 my-2">Win Odds: <?php echo $odds; ?></h4>
        </div>
    </div>
    <div class="row mx-4 my-4 justify-content-center">
        <a class='btn btn-warning btn-sm mx-0 my-1' href='games.php'>Return to Games Overview</a>
    </div>
</div>

<?php
    include_once("footer.php");
?>