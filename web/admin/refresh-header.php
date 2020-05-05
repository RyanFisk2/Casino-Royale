<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Poker Odds Calculator</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    	<link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico"/>
        <meta http-equiv="refresh" content="5">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark navbar-expand-sm">
            <a href="games.php" class="navbar-brand">Poker Odds Calculator</a>
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a href="https://github.com/gwu-iot/20_casino_royale" target="blank" class="nav-link">Source Code</a>
				</li>
				<li class="nav-item">
					<a href="games.php" class="nav-link">Games</a>
				</li>
				<li class="nav-item">
					<a href="users.php" class="nav-link">Users</a>
				</li>
			</ul>
            <form method="get" action="../includes/logout.inc.php">	
			<button type="submit" class="btn btn-primary btn-sm">
	          <span class="glyphicon glyphicon-log-out"></span> Log out
	        </button>
			</form>
        </nav>