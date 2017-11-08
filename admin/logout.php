<?php
	session_start();
	require_once("../assets/required/config.php");
	require_once ('backend/functions.php');

	if (!userLoggedIn()) {
		redirectLogin();
	}
	session_destroy();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="refresh" content="6; url=login.php">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../assets/themes/clean-and-elegant/admin.css" />
		<title>Logout Success</title>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="Absolute-Center is-Responsive">
					<h2>Logged out!</h2>
					<p>You have been successfully logged out. Redirecting to login page...</p>
				</div>
			</div>
		</div>
	</body>
</html>