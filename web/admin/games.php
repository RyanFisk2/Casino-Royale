<?php
    include_once('header.php');
    include_once('../includes/Database.php');
?>
<div class="container-fluid">
<div class="row justify-content-center">
        
        <div class="col col-6 align-self-center">
            <div class="card-header">
					<nav class="navbar navbar-light bg-light">
						<a class="navbar-brand"><b>Games</b></a>
						<a href="new-game.php?id=<?php echo $_SESSION['api_key']; ?>" class="btn btn-success" role="button">Create New Game</a>
					</nav>
			</div>
        </div>
        </div>

        <div class="row justify-content-center">
        <div class="col col-12">
            <div class="card-body">
                <?php
                    $table = "";
                    
                    $db = new Database();
                    $conn = $db->getConnection();

                    $query = "SELECT * FROM games";

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
                                        </td>";
                                if($row['state'] == 1) {
                                    $table .= "
                                            <td>
                                                <a class='btn btn-warning btn-sm mx-0 my-1' href='end-game.php?id=" . $row['game_id'] . "' role='button'>End Game</a>
                                            </td>";
                                } else {
                                    $table .= "<td>
                                                <a class='btn btn-danger btn-sm mx-0 my-1' href='delete-game.php?id=" . $row['game_id'] . "' role='button'>Delete</a>
                                                </td>";
                                }
                                $table .="  
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