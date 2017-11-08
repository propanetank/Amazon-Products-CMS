<?php
	require_once ('../assets/required/config.php');
	require_once ('backend/functions.php');
	session_start();
	
//	checkDatabaseConn();
	if (userLoggedIn()) {
		redirectHome();
	}
//	ssl();

	if(isset($_COOKIE['username']))
		$username = $_COOKIE['username'];
	if(isset($_GET['username']))
		$username = $_GET['username'];	
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../assets/themes/clean-and-elegant/admin.css" />
		<title>Login</title>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="Absolute-Center is-Responsive">
					<h1>Login</h1>
					<p>Please enter your login credentials to continue.</p>
					<span id="error">
						<?php if(isset($_GET['error'])) {
							echo "<p class=\"text-danger\">" . $_SESSION['errtxt'] . "</p>";
							unset($_SESSION['errtxt']);
						} ?>
					</span>
					<form id="login" action="backend/login.php" method="post" />
						<div class="form-group">
							<input type="text" name="username" class="form-control" value="<?php echo $username; ?>" pattern="[A-Za-z0-9]+" maxlength="20" placeholder="Username" autofocus required /><br />
						</div>
						<div class="form-group">
							<input type="password" name="password" class="form-control" placeholder="Password" maxlength="25" required /><br />
						</div>
						<label for="remember">Remember Me: </label>&nbsp;&nbsp;<input type="checkbox" name="remember" value="yes" checked /><br />
						<button type="submit" class="btn btn-primary">Login</button>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>