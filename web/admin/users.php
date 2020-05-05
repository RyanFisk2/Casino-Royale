<?php
    include_once('header.php');
    include_once('../includes/Database.php');
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        
        <div class="col col-6 align-self-center">
            <div class="card-header">
					<nav class="navbar navbar-light bg-light">
						<a class="navbar-brand"><b>Users</b></a>
						<a href="new-user.php" class="btn btn-success" role="button">Create New User</a>
					</nav>
			</div>
        </div>
        </div>
        <div class="row">
        <div class="col col-12">
            <div class="card-body">
                <?php
                    $table = "";
                    
                    $db = new Database();
                    $conn = $db->getConnection();

                    $query = "SELECT * FROM users";

                    try {
                        $stmt = $conn->prepare($query);
                        $result = $stmt->execute();

                        if ($result) {
                            $table .= "<div class='container'>
                                        <div class='table-responsive'>
                                            <table class='table table-hover'>
                                                <thead>
                                                    <tr>
                                                        <th>User ID</th>
                                                        <th>Username</th>
                                                        <th>Admin</th>
                                                        <th>API Key</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $admin_word = "";
                                if ($row['is_admin'] == 1) {
                                    $admin_word = "Yes";
                                } else {
                                    $admin_word = "No";
                                }
                                $table .= "
                                    <tr>
                                        <td>" . $row['user_id'] . "</td>
                                        <td>" . $row['username'] . "</td>
                                        <td>" . $admin_word . "</td>
                                        <td>" . $row['api_key'] . "</td>
                                        <td>
                                            <a class='btn btn-warning btn-sm mx-0 my-1' href='new-key.php?id=" . $row['user_id'] . "' role='button'>New API Key</a>
                                        </td>
                                        <td>
                                            <a class='btn btn-danger btn-sm mx-0 my-1' href='delete-user.php?id=" . $row['user_id'] . "' role='button'>Delete</a>
                                        </td>
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