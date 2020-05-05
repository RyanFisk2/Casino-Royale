<?php
    include_once('header.php');
    include_once('../includes/Database.php');
?>
<div class="container-fluid">

        <div class="row justify-content-center">
        <div class="col col-12">
            <div class="card-body">
                <?php
                    $table = "";
                    
                    $db = new Database();
                    $conn = $db->getConnection();

                    $query = "SELECT * FROM games WHERE created_by=" . $_SESSION['user_id'];

                    try {
                        $stmt = $conn->prepare($query);
                        $result = $stmt->execute();

                        if ($result) {
                            $table .= "<div class='container'>
                                        <div class='table-responsive'>
                                            <table class='table table-hover'>
                                                <thead>
                                                    <tr>
                                                        <th>Game ID</th>
                                                        <th>Created By</th>
                                                        <th>Scanned Cards</th>
                                                        <th>State</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $state_word = "";
                                if (($row['state'] == 1) && ($row['scanned_cards'] < 7)) {
                                    $state_word = "Active, missing cards";
                                } else if (($row['state'] == 0) && ($row['scanned_cards'] < 7)) {
                                    $state_word = "Inactive, incomplete";
                                } else if ((($row['state'] == 0) && ($row['scanned_cards'] == 7))) {
                                    $state_word = "Inactive, complete";
                                } else if (($row['state'] == 1) && ($row['scanned_cards'] < 7)) {
                                    $state_word = "Active, all cards scanned";
                                }
                                $table .= "
                                    <tr>
                                        <td>" . $row['game_id'] . "</td>
                                        <td>" . $row['created_by'] . "</td>
                                        <td>" . $row['scanned_cards'] . "</td>
                                        <td>" . $state_word . "</td>
                                        <td>
                                            <a class='btn btn-warning btn-sm mx-0 my-1' href='view-game.php?id=" . $row['game_id'] . "' role='button'>Details</a>
                                        </td
                                    </tr>
                                ";
                            }

                            $table .= "</tbody></table></div></div>";
                            echo $table;
                        }
                    } catch (PDOException $e) {
                        echo "DB Problem: " . $e->getMessage();
                        return false;
                    }
                ?>
            </div>
        </div>
    </>
</div>
<?php
    include_once('footer.php');
?>