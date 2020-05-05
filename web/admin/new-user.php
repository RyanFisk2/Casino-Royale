<?php
    include_once('header.php');
?>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-6 mt-5 mx-5">
				<div class="card">
					<div class="card-header">
						<h3 class="text-center">Create an Account</h3>
					</div>
					<div class="card-body">
						<form action="../includes/register-admin.inc.php" method="post">
							<div class="form-row mt-4">
								<div class="form-group w-50">
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
								<div class="form-group w-50">
										<label for="admin">Is Administrator?</label>
										<input type="checkbox" class="form-control" name="admin" value="Yes">
									</div>
								</div>
							<div class="form-row mt-4">
								<div class="form-group col align-self-center my-2">
									<button type="submit" class="btn btn-success btn-block" name="create-submit">Create Account</button>
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
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='new-user.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
									if($_GET['e'] == 2){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Registration Unsucessful</strong> <br />Username already in use!
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='new-user.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
                                    }
                                    if($_GET['e'] == 3){
										echo "<div class='alert alert-danger alert-dismissible fade show text-center' role='alert'>
												<strong>Registration Unsucessful</strong> <br />Passwords don't match!
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='new-user.php'>
													<span aria-hidden='true'>&times;</span>
												</button>
											</div>";
									}
								} if (isset($_GET['c'])) {
									if ($_GET['c'] == 1) {
										echo "<div class='alert alert-success alert-dismissible fade show text-center' role='alert'>
												<strong>Account Successfully Registered!</strong> <br />
												<button type='button' class='close' data-dismiss='alert' aria-label='Close' href='new-user.php'>
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
	</div>
<?php
    include_once('footer.php');
?>