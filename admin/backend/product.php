<?php
	require_once("../../assets/required/config.php");
	require_once ('functions.php');
	session_start();
	
	checkDatabaseConn();
	if (!userLoggedIn())
		redirectLogin();

	if ($_SERVER['REQUEST_METHOD'] != 'POST')
		header("Location: " . SITE_URL . PATH . "dashboard.php");

	if (empty($_POST['itemid']))

?>