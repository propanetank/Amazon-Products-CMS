<?php
	require_once ('../assets/required/config.php');
	require_once ('backend/functions.php');
	session_start();
	
	checkDatabaseConn();
	if (!userLoggedIn())
		redirectLogin();
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
		<title>Dashboard</title>
	</head>
	<body>
		<header>
			<?php require_once ('backend/navigation.php'); ?>
		</header>
		<div class="container">
			<h1 class="text-center">Content Dashboard</h1>
			<p class="text-center">From here add products for display, manage existing products, and </p>
			<div class="row">
				<div class="col">
					<h2>Add Product</h2>
					<p>Fill out everything below to get the information from amazon.</p>
					<p class="text-primary"><b>Be sure you added your Amazon Product API marketplace, associate tag, access ID, and secret to the config file found in /assets/required/config.php, otherwise adding a product will fail.</b></p>
					<form>
						<div class="form-group">
							<label for="itemid">Enter item ID and pick the type of ID entered.</label><br />
							<input type="text" class="form-control" name="itemid" placeholder="Item ID" pattern="[0-9]+" required />
							<select name="idtype[]" required>
								<option value="asin" selected>ASIN</option>
								<option value="sku">SKU</option>
								<option value="upc">UPC</option>
								<option value="ean">EAN</option>
								<option value="isbn">ISBN</option>
							</select>
						</div>
						<div class="form-group">
							<label for="attributes">Select the data to get for the item. Use control to select multiple, or shift to select a range, hover over an option for a brief description</label><br />
							<select name="attributes[]" multiple required>
								<option value="images" title="Gets URLs of all images in small, medium, and large" selected>Images</option>
								<option value="attributes" title="Gets attributes that describe the item, might return a lot of data">Item Attributes</option>
								<option value="reviews" title="Get iframe link to reviews" selected>Reviews</option>
								<option value="price" title="Get price information" selected>Price</option>
								<option value="tracks" title="Gets track information on CDs">Tracks</option>
							</select>
						</div>
					</form>
				</div>
				<div class="col">
					<h2>Delete Product</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h2>Manage Products</h2>
				</div>
			</div>
		</div>
	</body>
</html>