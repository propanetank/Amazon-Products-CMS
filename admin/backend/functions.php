<?php
	// Begin Functions
	$conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	function checkDatabaseConn() {
		global $conn;
		if($conn->connect_error) {
			echo "<h3 class='text-danger'>Error connecting to the database, unable to fetch data!</h3>";
			echo "<p class='text-danger'>Please try loading the page in a few minutes.</p>";
			die($conn->connect_error);
		}
	}

	function checkAPI() {
		// Check if the API info has been set, if not, thow an error to update the info
		if (!defined('ACCESSKEY') || !defined('SECRET') || !defined('ASSOCIATEID')) {
			echo "<p class='text-danger'><b>Error, one or more API settings is not specified. These need to be added for this to work, they are found in /assets/required/config.php</b></p>";
		}
	}

/*	function ssl() {
		if (SSL) {
			if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			}
		}
	}
*/

	function redirectLogin() {
		header("Location: " . SITE_URL . PATH . "login.php");
	}

	function redirectHome() {
		header("Location: " . SITE_URL . PATH . "dashboard.php");
	}

	function userLoggedIn() {
		if(isset($_SESSION['isloggedin'])) {
			return true;
		} else {
			return false;
		}
	}

	function sanitizeData($badData) {
		$goodData = trim($badData);
		$goodData = stripslashes($badData);
		$goodData = htmlspecialchars($badData);
		return $goodData;
	}

	function changePassword() {
		if (isset($_SESSION['resetRequired'])) {
			if ($_SERVER['SCRIPT_NAME'] != PATH . 'changePassword.php')
				return true;
		} else
			return false;
	}

	function getUserPassword() {
		// Get the salt from the users password
		global $conn;
		if (isset($_SESSION['isloggedin']))
			$userSalt = "SELECT password FROM users WHERE id='$_SESSION[uid]'";  // User logged in, use userid
		else
			$userSalt = "SELECT password FROM users WHERE username='$_POST[username]'";  // User not logged in, use username (probably at login page)
		$getSalt = $conn->query($userSalt);
		if ($getSalt->num_rows > 0) {
			$pass[0] = $getSalt->fetch_object()->password;  // The entire hash
			$pass[1] = substr($pass[0], 3, 10);  // The salt, salt is 10 characters long, change the 10 to change salt size
			return $pass;  // Return array containing the entire hash (pos 0) and the salt (pos 1)
		} else {
			return false;
		}
	}
?>