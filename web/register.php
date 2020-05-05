<!DOCTYPE html>
<html>
<head>
        <title>Poker Odds Calculator</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    	<link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico"/>
</head>
<body>
		<nav class="navbar navbar-dark bg-dark navbar-expand-sm">
            <a href="index.php" class="navbar-brand">Poker Odds Calculator</a>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="https://github.com/gwu-iot/20_casino_royale" target="blank" class="nav-link">Source Code</a>
				</li>
			</ul>
        </nav>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-6 mt-5 mx-5">
				<div class="card">
					<div class="card-header">
						<h3 class="text-center">Claim Your Account</h3>
					</div>
					<div class="card-body">
						<form action="includes/register.inc.php" method="post">
							<div class="form-row mt-4">
								<div class="form-group w-100">
									<label for="username">Set Username</label>
									<input type="text" class="form-control" name="username" placeholder="Username">
								</div>
							</div>
							<div class="form-row mt-4">
								<div class="form-group col-md-6">
									<label for="password">Set Password</label>
									<input type="password" class="form-control" name="password" placeholder="Password">
								</div>
								<div class="form-group col-md-6">
									<label for="reppassword">Repeat Password</label>
									<input type="password" class="form-control" name="reppassword" placeholder="Repeat Password">
								</div>
							</div>
							<div class="form-row mt-4">
								<div class="form-group col align-self-center my-2">
									<button type="submit" class="btn btn-success btn-block" name="claim-submit">Claim Account</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col mt-5 mx-5">
						<div class="errors">
							<?php
								if(isset($_GET['e'])){
									if($_GET['e'] == 1){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Registration Unsucessful</strong> <br />Missing details! Username and Password are required.
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='register.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
									if($_GET['e'] == 2){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Registration Unsucessful</strong> <br />Username already in use!
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='register.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
                                    }
                                    if($_GET['e'] == 3){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Registration Unsucessful</strong> <br />Passwords don't match!
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='register.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
								} if (isset($_GET['c'])) {
									if ($_GET['c'] == 1) {
										echo "<div class='alert alert-success alert-dismissible fade show text-center' role='alert'>
												<strong>Account Successfully Registered!</strong> <br />Click <a href='index.php' class='alert-link'>here</a> to log in.
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='register.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="row justify-content-center">
			<a href="index.php" class="btn btn-info" role="button">Return to Login</a>
		</div>
	</div>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>