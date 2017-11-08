<?php
	// if the user checked the 'remember me' box, have the session last for 30 days instead of the default php value
	if (isset($_POST['remember'])) {
		session_set_cookie_params(2592000);
		$_SESSION['remember'] = 'yes';
	}
	require_once("../../assets/required/config.php");
	require_once ('functions.php');
	session_start();
	
	checkDatabaseConn();
	if (userLoggedIn()) {
		redirectHome();
	}

	// Make sure they actually sent a username or password
	if(empty($_POST['username']) || empty($_POST['password'])) {
		$_SESSION['errtxt'] = "You must enter a username and password.";
		
		// If username is provided, sanitize it
		if (!empty($_POST['username']))
			$username = sanitizeData($_POST['username']);
		header("Location: " . SITE_URL . PATH . "login.php?error=true&username=" . $username);
	}

	// Passwords are stored as salted sha-512 in format $6$salt$encpassword, I use mkpasswd -m sha-512 password salt randomsalt
	// Currently salt is set to 10 characters, can be changed in functions.php
	// Get the salt from the users password
	$pass = getUserPassword();
	if ($pass === FALSE) {
		goto end;
	}
	$salt = $pass[1];

	$username = $_POST['username'];

	// Sanitize the username
	$username = sanitizeData($_POST['username']);
	$password = crypt($_POST['password'], '$6$' . $salt);


	$loginSQL = "SELECT id, name FROM users WHERE username='$username' AND password='$password'";
	$checkLogin = $conn->query($loginSQL);
	if ($checkLogin->num_rows > 0) {
		// Sucessful login, redirect to homepage.
		$_SESSION['isloggedin'] = true;
		$_SESSION['username'] = $username;
		while ($row = $checkLogin->fetch_assoc()) {
			$_SESSION['uid'] = $row['id']; 
			$_SESSION['name'] = $row['name'];
		}
		redirectHome();
	} else {
		end:
		// Incorrect login, redirect back to login page and give them an error
		$_SESSION['errtxt'] = "Username and/or password incorrect, please try again.";
		header("Location: " . SITE_URL . PATH . "login.php?error=true&username=" . $username);
	}
?>