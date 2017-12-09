<?php
	require_once ('../assets/required/config.php');
	require_once ('backend/functions.php');
	session_start();

	if (!userLoggedIn())
		redirectLogin();
	else
		redirectHome();

?>