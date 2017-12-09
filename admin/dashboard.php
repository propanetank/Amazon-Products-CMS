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
			<?php
				if(!(file_exists('backend/vendor/marcl/amazonproductapi')))
					echo "<p class='text-danger'>It doesn't appear you have the Amazon Product API library installed. Have you installed it with composer and moved the vendor folder to /admin/backend? Install it by running: <pre>composer require marcl/amazonproductapi</pre></p>";
			?>
			<div class="row">
				<div class="col">
					<h2>Add Product</h2>
					<?php
					// Display any errors and unset their variable
					if(isset($_GET['errors']) && $_GET['errors'] === true) {
						if(isset($_SESSION['asinErr'])) {
							echo "<p class='text-danger'>" . $_SESSION['asinErr'] . "</p>";
							unset($_SESSION['asinErr']);
						}
						if(isset($_SESSION['onlyAmazonErr'])) {
							echo "<p class='text-danger'>" . $_SESSION['onlyAmazonErr'] . "</p>";
							unset($_SESSION['onlyAmazonErr']);
						}
					}

					// Display what was added/updated to/in the database and unset the variable
					if (isset($_GET['success']) && $_GET['success'] == "true") {
						echo "<div class='alert alert-success'>";
						echo "<p>Successfully added or updated the following product!<br />";
						echo "Title: " . $_SESSION['product']['title'] . "<br />";
						echo "Category: " . $_SESSION['product']['category'] . "</p>";
						echo "</div>";
						unset($_SESSION['product']);
					} else if (isset($_GET['success']) && $_GET['success'] == "false") {
						echo "<div class='alert alert-danger'>";
						echo $_SESSION['prodErrTxt'];
						echo "</div>";
						unset($_SESSION['prodErrTxt']);
					}
					?>
					<p>Fill out everything below to get the information from amazon, it will then be stored in the database.</p>
					<p class="text-primary"><b>Be sure you added your Amazon Product API marketplace, associate tag, access ID, and secret to the config file found in /assets/required/config.php, otherwise adding a product will fail.</b></p>
					<form action="backend/product.php?action=new" method="post">
						<div class="form-group">
							<label for="itemid">Enter item ID and pick the type of ID entered.</label><br />
							<input type="text" class="form-control" name="itemid" placeholder="Item ID" pattern="[A-Za-z0-9]+" required />
							<select name="idtype[]" required>
								<option value="asin" selected>ASIN</option>
						<!--	Library being used to call the API only supports ASIN at the current time of code writing
								<option value="sku">SKU</option>
								<option value="upc">UPC</option>
								<option value="ean">EAN</option>
								<option value="isbn">ISBN</option> -->
							</select>
						</div>
				<!--	Library also doesn't yet support selecting specific attributes
						<div class="form-group">
							<label for="attributes">Select the data to get for the item. Use control to select multiple, or shift to select a range, hover over an option for a brief description</label><br />
							<select name="attributes[]" multiple required>
								<option value="images" title="Gets URLs of all images in small, medium, and large" selected>Images</option>
								<option value="attributes" title="Gets attributes that describe the item, might return a lot of data">Item Attributes</option>
								<option value="reviews" title="Get iframe link to reviews" selected>Reviews</option>
								<option value="price" title="Get price information" selected>Price</option>
								<option value="tracks" title="Gets track information on CDs">Tracks</option>
							</select>
						</div> -->
						<div class="form-group">
							<label for="onlyAmazon">Only get items sold my Amazon?</label>
							<input type="radio" name="onlyAmazon" value="true" checked required> Yes &nbsp;
							<input type="radio" name="onlyAmazon" value="false"> No
						</div>
						<button type="submit" class="btn btn-primary">Add Product</button>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h2>Manage Products</h2>
					<?php
					$getProducts = "SELECT * FROM products WHERE deleted='n'";
					$getCategories = "SELECT category FROM products";
					$result = $conn->query($getCategories);
					if ($result->num_rows > 0) {
						echo "<datalist id='categories'>";
						while ($row = $result->fetch_object()->category)
							echo "<option value='" . $row . "'>";
						echo "</datalist>";
					}
					$result = $conn->query($getProducts);
					if ($result->num_rows > 0) {
						echo "<div style=\"overflow-x: auto;\">";
						echo "<table class='table-striped'>
								<thead class='thead-light'>
									<tr>
										<th>Item</th>
										<th>Images</th>
										<th>Price</th>
										<th>Category</th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</thead>";
						while ($row = $result->fetch_assoc()) {
							$id = $row['id'];
							echo "<tr><form action='backend/product.php?action=update' method='post'>";
								echo "<td width='40%' style='overflow-x: auto;'><input type='hidden' name='id' value='" . $id . "' /><input type='text' name='title' size='38' maxlength='150' value='" . $row["title"] . "' required /></td>";
								$images = explode(" ", $row['images']);
								$productNumber = 0;
								echo "<td><div id='product" . $productNumber . "' class='carousel slide' data-interval='false'>
									<ul class='carousel-indicators'>";
								for ($i=0; $i < (count($images) - 1); $i++) {
									if ($i == 0) {
										echo "<li data-target='#product" . $productNumber . "' data-slide-to='" . $i . "' class='active'></li>";
										continue;
									}
									echo "<li data-target='#product" . $productNumber . "' data-slide-to='" . $i . "'></li>";
								}
								echo "</ul>
									  <div class='carousel-inner'>";
								for ($i=0; $i < (count($images) - 1); $i++) {
									if ($i == 0) {
										echo "<div class='carousel-item active'><img src='" . $images[$i] . "' width='100px' alt='image" . $i . "' /></div>";
										continue;
									}
									echo "<div class='carousel-item'><img src='" . $images[$i] . "' width='100px' alt='image" . $i . "' /></div>";
								}
								// Close <div class="carousel-inner">
								echo "</div>
									  <a class='carousel-control-prev' href='#product" . $productNumber . "' data-slide='prev'><span class='carousel-control-prev-icon'></span></a>
									  <a class='carousel-control-next' href='#product" . $productNumber . "' data-slide='next'><span class='carousel-control-next-icon'></span></a>";
								// Close <div id="product#" class="carousel slide" data-interval="false">
								echo "</div></td>";
								echo "<td>" . $row['price'] . "</td>";
								echo "<td><input list='categories' name='category' size='15' maxlength='150' value='" . $row['category'] . "' required /></td>";
								echo "<td><button type='submit' class='btn btn-primary'>Update</button></td>";
								echo "</form><form action='backend/product.php&?ction=amazon' method='post'>";
								echo "<td><input type='hidden' name='id' value='" . $id . "' /><span title='Updates product entry with Amazon information, same affect as deleting the product and re-adding it'><button type='submit' class='btn btn-primary'>Update from Amazon</button></span></td>";
								echo "</form><form action='backend/product.php?action=delete' method='post'>";
								echo "<td><input type='hidden' name='id' value='" . $id . "' /><span title='This cannot be undone!'><button class='btn btn-primary'>Delete</button></span></td>";
								echo "</form>";
							echo "</tr>";
							$productNumber++;
						}
						echo "</table>";
						echo "</div>\n";
						unset($productNumber);
					} else {
						echo "<p class='text-danger'>No products found in the database. Add some above.</p>";
					}
					?>
				</div>
			</div>
		</div>
	</body>
</html>