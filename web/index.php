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
				<div class="card align-text-center">
					<div class="card-header">
						<h3 class="text-center"><b>Log In</b></h3>
					</div>
					<div class="card-body">
						<form action="includes/login.inc.php" method="post">
							<div class="form-row">
								<div class="form-group col">
									<label for="username">Username</label>
									<input type="text" class="form-control" name="username" placeholder="Username">
								</div>
							</div>
							<div class="form-row mt-4">
								<div class="form-group col">
									<label for="pwd">Password</label>
									<input type="password" class="form-control" name="pwd" placeholder="Password">
								</div>
							</div>
							<div class="form-row mt-4">
								<div class="form-group col align-self-center my-2">
									<button type="submit" class="btn btn-success btn-block" name="login-submit">Login</button>
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
										echo "<div class='alert alert-warning alert-dismissible fade show text-center' role='alert'>
												<strong>Login Unsucessful</strong> <br />Missing details! Username and Password are required.
												<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
									if($_GET['e'] == 2){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Login Unsucessful</strong> <br />Username not found! Would you like to <a href='register.php' class='alert-link'>Create an Account?</a>
												<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
									if($_GET['e'] == 3){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Login Unsucessful</strong> <br />Incorrect Login Information.
												<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
								} if (isset($_GET['l'])) {
									if ($_GET['l'] == 1) {
										echo "<div class='alert alert-success alert-dismissible fade show text-center' role='alert'>
												<strong>You've been Logged Out!</strong>
												<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
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
		</iframe>
		<div class="row justify-content-center my-0">
			<a href="register.php" class="btn btn-info" role="button">New? Create an account!</a>
		</div>
	</div>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>